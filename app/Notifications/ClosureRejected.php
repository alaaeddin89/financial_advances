<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClosureRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $invoiceId;
    protected $advanceId;
    protected $rejectionReason;

    /**
     * إنشاء مثيل جديد للإشعار.
     *
     * @param int $invoiceId معرف الفاتورة المرفوضة
     * @param int $advanceId معرف العهدة المرتبطة
     * @param string $rejectionReason سبب الرفض
     * @return void
     */
    public function __construct($invoiceId, $advanceId, $rejectionReason)
    {
        $this->invoiceId = $invoiceId;
        $this->advanceId = $advanceId;
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * الحصول على قنوات التوصيل للإشعار.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // نرسل الإشعار عبر قناة قاعدة البيانات (Database) ليظهر في إشعارات التطبيق
        // يمكن إضافة 'mail' إذا أردنا إرسال بريد إلكتروني أيضاً
        return ['database']; 
    }

    /**
     * الحصول على تمثيل الإشعار لقاعدة البيانات (يتم تخزينه في جدول الإشعارات).
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'closure_rejected',
            'invoice_id' => $this->invoiceId,
            'advance_id' => $this->advanceId,
            'reason' => $this->rejectionReason,
            'message' => 'تم رفض تقفيل الفاتورة رقم ' . $this->invoiceId . ' (العهدة رقم ' . $this->advanceId . ') بسبب: ' . $this->rejectionReason,
            'link' => route('invoices.show',$this->invoiceId),
        
        ];
    }
}
