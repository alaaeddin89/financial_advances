<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Helper\Helper;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if(!Helper::checkPermission(26))
            return redirect()->back()->with("error","لايوجد صلاحيات للوصول الى النموذج المطلوب");


        if ($request->ajax()) {
            $suppliers = Supplier::with('creator')->select('suppliers.*');

            return DataTables::of($suppliers)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    return $row->name_ar . ($row->name_en ? ' - '.$row->name_en : '');
                })
                ->addColumn('balance', function ($row) {
                    // إذا عندك علاقة حسابات (account) ممكن تستبدل هذا الكود
                    return $row->invoices()->sum('amount') . ' ر.س';
                })
                ->addColumn('contact_person', function ($row) {
                    return $row->creator ? $row->creator->full_name : '-';
                })
                ->editColumn('address', function ($row) {
                    return $row->national_address ?: '-';
                })
                ->addColumn('action', function ($row) {
                    
                    $deleteUrl = route('suppliers.destroy', $row->id);
                    $showUrl = route('suppliers.show', $row->id);
                    return '
                    <button class="btn btn-sm btn-primary editSupplier" data-id="'.$row->id.'">
                        <i class="fa fa-edit"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-light-danger" 
                            onclick="deleteRecord('.$row->id.')">
                        <i class="fa fa-trash"></i>
                    </button>

                    <a href="'.$showUrl.'" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                    
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('suppliers.index');
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:100',
            'tax_id_no' => 'required|string|max:30',
            'commercial_register_no' => 'required|string|max:100',
            'national_address' => 'required|string|max:255',
            'attachments.*' => 'file|max:2048|mimes:jpg,png,pdf,jpeg',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/suppliers/' . auth()->id(), 'public');
                $attachments[] = "/storage/" . $path;
            }
        }

        $supplier = Supplier::create([
            'user_id' => auth()->id(),
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'tax_id_no' => $request->tax_id_no,
            'commercial_register_no' => $request->commercial_register_no,
            'phone' => $request->phone,
            'national_address' => $request->national_address,
            'building_number' => $request->building_number,
            'sub_number' => $request->sub_number,
            'attachments' => json_encode($attachments, JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json(['success' => true, 'data' => $supplier]);
    }

    public function edit(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name_ar' => 'required|string|max:100',
            'tax_id_no' => 'required|string|max:30',
            'commercial_register_no' => 'required|string|max:100',
            'national_address' => 'required|string|max:255',
            'attachments.*' => 'file|max:2048|mimes:jpg,png,pdf,jpeg',
        ]);

        // تجهيز البيانات العادية (بدون attachments)
        $data = $request->only([
            'name_ar',
            'name_en',
            'tax_id_no',
            'commercial_register_no',
            'phone',
            'national_address',
            'building_number',
            'sub_number',
        ]);

        // التعامل مع الملفات الجديدة
        if ($request->hasFile('attachments')) {
            $newFiles = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/suppliers/' . auth()->id(), 'public');
                $newFiles[] = "/storage/" . $path;
            }

            // دمج الجديد مع القديم
            $oldAttachments = $supplier->attachments ?? [];
            $data['attachments'] = json_encode(array_merge($oldAttachments, $newFiles), JSON_UNESCAPED_UNICODE);
        }

        // تحديث المورد
        $supplier->update($data);

        return response()->json(['success' => true, 'data' => $supplier]);
    }


    public function destroy(Supplier $supplier)
    {
        if(!Helper::checkPermission(26))
            return response()->json([
                'success' => false, 
                'message'=>"لايوجد صلاحيات للوصول الى النموذج المطلوب"
            ], 403); // استخدام 403 Forbidden

        // فحص الفواتير المرتبطة
        if ($supplier->invoices()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المورد لوجود فواتير مرتبطة به.',
            ], 409); 
        }

        $supplier->delete();
        
        //  إضافة رسالة النجاح
        return response()->json([
            'success' => true,
            'message' => 'تم حذف المورد بنجاح.',
        ]);
    }


    public function uploadAttachments(Request $request, Supplier $supplier)
    {
        $request->validate([
            'attachments.*' => 'file|max:2048|mimes:jpg,png,pdf,jpeg',
        ]);

        $attachments = $supplier->attachments ?? [];

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('uploads/suppliers/' . auth()->id(), 'public');
                $attachments[] = "/storage/" . $path;
            }
        }

        $supplier->attachments = $attachments;
        $supplier->save();

        return response()->json([
            'success' => true,
            'message' => 'تم رفع الملفات بنجاح',
            'attachments' => $attachments
        ]);
    }

    public function deleteAttachment(Supplier $supplier, $index)
    {
        $attachments = $supplier->attachments ?? [];

        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $supplier->attachments = array_values($attachments); // إعادة الترتيب
            $supplier->save();

            return response()->json(['success' => true, 'message' => 'تم حذف المرفق بنجاح']);
        }

        return response()->json(['success' => false, 'message' => 'المرفق غير موجود'], 404);
    }


    public function show(Supplier $supplier)
    {
        // إذا عندك أعمدة JSON مثل attachments لازم تعمل cast لها في الـ Model
        // $supplier->attachments = json_decode($supplier->attachments, true);

        return view('suppliers.show', compact('supplier'));
    }



}
