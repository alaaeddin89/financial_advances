<?php

namespace App\Services;

use App\Models\FinancialAdvance;
use App\Models\ExpenseInvoice;
use App\Models\AdvanceInvoiceClosure;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClosureService
{
    /**
     * يقوم بإجراء عملية تقفيل العهدة باستخدام مصفوفة من الفواتير والمبالغ.
     * * @param FinancialAdvance $advance العهدة المراد تقفيلها
     * @param array $invoicesData مصفوفة تحتوي على [invoice_id, closed_amount] لكل فاتورة
     * @param int $closerId معرف الموظف القائم بالتقفيل
     * @return array مصفوفة بسجلات التقفيل الجديدة (AdvanceInvoiceClosure)
     */
    public function performMultiInvoiceClosure(FinancialAdvance $advance, array $invoicesData, int $closerId): array
    {
        $totalClosedAmount = 0;
        $closures = [];

        // 1. التحقق من القيود الأساسية للعهدة والمدخلات
        if ($advance->status !== 'Confirmed' && $advance->status !== 'Partially Closed') {
            throw new \Exception('لا يمكن التقفيل: العهدة ليست مؤكدة أو مقفلة جزئياً.');
        }

        if (empty($invoicesData)) {
            throw new \Exception('يجب اختيار فاتورة واحدة على الأقل لإجراء التقفيل.');
        }

        // 2. بدء معاملة قاعدة البيانات لضمان سلامة البيانات
        return DB::transaction(function () use ($advance, $invoicesData, $closerId, &$totalClosedAmount, &$closures) {
            
            $invoiceModels = [];
            
            // تحقق من صلاحية ومبالغ جميع الفواتير أولاً
            foreach ($invoicesData as $data) {
                
                $invoice = ExpenseInvoice::findOrFail($data['invoice_id']);
                // تحويل المبلغ إلى float لضمان دقة العمليات
                $closedAmount = (float) $data['closed_amount']; 
                
                if ($invoice->status !== 'Approved') {
                    throw new \Exception("لا يمكن التقفيل: حالة الفاتورة رقم {$invoice->id} يجب أن تكون 'Approved'.");
                }

                $invoiceRemainingAmount = $invoice->amount - $invoice->used_amount;
                
                if ($closedAmount <= 0.00) {
                    throw new \Exception("مبلغ التقفيل للفاتورة رقم {$invoice->id} يجب أن يكون أكبر من الصفر.");
                }

                if ($closedAmount > $invoiceRemainingAmount) {
                    throw new \Exception("مبلغ التقفيل ({$closedAmount}) يتجاوز المبلغ المتاح في الفاتورة رقم {$invoice->id} ({$invoiceRemainingAmount}).");
                }
                
                // تخزين الفاتورة ومبلغها لبدء التقفيل الفعلي لاحقاً
                $invoiceModels[] = [
                    'model' => $invoice,
                    'closed_amount' => $closedAmount
                ];
                
                $totalClosedAmount += $closedAmount;
            }
            
            // التحقق النهائي لرصيد العهدة
            if ($totalClosedAmount > $advance->remaining_balance) {
                throw new \Exception('إجمالي مبلغ التقفيل يتجاوز الرصيد المتبقي للعهدة.');
            }

            // 3. تنفيذ التقفيل على كل فاتورة على حدة
            foreach ($invoiceModels as $item) {
                $invoice = $item['model'];
                $closedAmount = $item['closed_amount'];

                // أ. إنشاء سجل تقفيل منفصل لكل فاتورة (هذا هو المفتاح للربط المتعدد)
                $closure = AdvanceInvoiceClosure::create([
                    'advance_id' => $advance->id,
                    'invoice_id' => $invoice->id,
                    'closed_amount' => $closedAmount,
                    'closure_date' => Carbon::now(),
                    'closed_by_user_id' => $closerId,
                ]);

                // ب. تحديث الفاتورة (زيادة المبلغ المستخدم)
                $invoice->used_amount += $closedAmount;
                $invoice->save();
                
                $closures[] = $closure;
            }

            // 4. تحديث العهدة (advances) مرة واحدة بالإجمالي
            $advance->closed_amount += $totalClosedAmount;
            $advance->remaining_balance = $advance->amount - $advance->closed_amount;
            
            // تحديد حالة العهدة
            if ($advance->remaining_balance <= 0.001) { // استخدام فرق بسيط لتجنب مشاكل النقطة العائمة
                $advance->status = 'Closed';
                $advance->remaining_balance = 0.00;
            } else {
                $advance->status = 'Partially Closed';
            }
            
            $advance->save();

            return $closures;
        });
    }

    /**
     * مغلِّف للحفاظ على توافق الدالة performAutoClosure
     * @return AdvanceInvoiceClosure سجل التقفيل الجديد
     */
    public function performClosure(FinancialAdvance $advance, ExpenseInvoice $invoice, float $closedAmount, int $closerId): AdvanceInvoiceClosure
    {
        // نستدعي الدالة الجديدة مع إدخال الفاتورة الواحدة في مصفوفة
        $invoicesData = [
            [
                'invoice_id' => $invoice->id,
                'closed_amount' => $closedAmount
            ]
        ];
        
        $closures = $this->performMultiInvoiceClosure($advance, $invoicesData, $closerId);
        
        // بما أنها عملية تقفيل واحدة، نُرجع أول سجل تقفيل
        return $closures[0];
    }
   


    /**
     * يقوم بإجراء تقفيل تسلسلي تلقائي لجميع العهد المفتوحة باستخدام الفواتير المتاحة.
     *
     * @param int $userId معرف الموظف
     * @return array تفاصيل عمليات التقفيل التي تمت
     */
    public function performAutoClosure(int $userId): array
    {
        return DB::transaction(function () use ($userId) {
            
            // 1. جلب العهد المتاحة للتقفيل، مرتبة حسب الأولوية (الأقدم أولاً)
            $advances = FinancialAdvance::where('user_id', $userId)
                ->whereIn('status', ['Confirmed', 'Partially Closed'])
                ->where('remaining_balance', '>', 0)
                ->orderBy('issue_date', 'asc') // الأقدم أولاً
                ->get();

            // 2. جلب الفواتير المتاحة للتقفيل، مرتبة حسب الأولوية (مثلاً الأقدم أولاً)
            $invoices = ExpenseInvoice::where('user_id', $userId)
                ->where('status', 'Approved')
                ->whereRaw('amount > used_amount')
                ->orderBy('invoice_date', 'asc')
                ->get();
            
            $closuresPerformed = [];
            $invoiceIndex = 0;

            // 3. بدء عملية التقفيل التسلسلي
            foreach ($advances as $advance) {
                
                $remainingAdvance = $advance->remaining_balance;
                
                // محاولة تقفيل العهدة الحالية باستخدام الفواتير المتاحة بالتتالي
                while ($remainingAdvance > 0.001 && $invoiceIndex < $invoices->count()) {
                    
                    $invoice = $invoices[$invoiceIndex];
                    $availableInvoiceAmount = $invoice->amount - $invoice->used_amount;

                    // المبلغ الذي يمكن تقفيله في هذه الجولة (الحد الأدنى من الاثنين)
                    $closureAmount = min($remainingAdvance, $availableInvoiceAmount);

                    if ($closureAmount > 0.001) {
                        try {
                            // استخدام دالة performClosure الأساسية لضمان تطبيق المنطق القياسي والتحديث
                            $closure = $this->performClosure($advance, $invoice, $closureAmount, $userId);
                            $closuresPerformed[] = $closure;
                            
                            // تحديث الأرصدة المتبقية في هذه الجولة
                            $remainingAdvance -= $closureAmount;
                            
                            // يجب تحديث الفاتورة داخل الحلقة بعد التقفيل
                            $invoice->refresh(); 
                            
                        } catch (\Exception $e) {
                            // في حال حدوث خطأ غير متوقع، إيقاف العملية
                            // يمكن تسجيل الخطأ هنا
                            break; 
                        }
                    }
                    
                    // إذا تم استنفاد الفاتورة الحالية بالكامل، انتقل إلى الفاتورة التالية
                    if ($invoice->amount - $invoice->used_amount <= 0.001) {
                        $invoiceIndex++;
                    } else {
                        // إذا كان هناك رصيد متبقٍ في الفاتورة، لكن العهدة أُغلقت، نكسر حلقة الفواتير وننتقل للعهدة التالية
                        break;
                    }
                }
                
                // إذا لم تعد هناك فواتير متاحة، نوقف عملية التقفيل الكلية
                if ($invoiceIndex >= $invoices->count()) {
                    break;
                }
            }

            return $closuresPerformed;
        });
    }
}