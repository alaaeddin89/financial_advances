<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قسائم الرواتب</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif; /* Required for Arabic */
            direction: rtl;
            text-align: right; /* Align text to the right */
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .section-title {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
            font-weight: bold;
        }
        .summary {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
        .highlight {
            background-color: #ffeb3b;
        }
    </style>
</head>
<body>
    @foreach($employeeSalaries as $empSal)
        <div class="container">
            <div class="section-title">قسيمة راتب {{ $empSal->master->month }}/{{ $empSal->master->year }}</div>
            <h3 style="background: lightslategrey">البيانات الادارية</h3>
            <table>
                <tr>
                    <th>الاسم</th>
                    <td colspan="2">{{ $empSal->emps->full_name }}</td>
                    <th>الرقم الوظيفي</th>
                    <td colspan="2">{{ $empSal->emps->job_id_no }}</td>
                </tr>
                <tr>
                    <th>موقع العمل</th>
                    <td>{{ $empSal->emps->location }}</td>
                    <th> الساحة</th>
                    <td>غزة</td>
                    <th> الدرجة</th>
                    <td>{{ $empSal->degree_name }}</td>
                </tr>
                <tr>
                    <th>المسمى الوظيفة</th>
                    <td>{{ $empSal->emps->jobs_name }}</td>
                    <th>طبيعة الاشراف</th>
                    <td colspan="3">{{ $supervision ?? '' }}</td>
                </tr>
                <tr>
                    <th>تاريخ بدء العمل</th>
                    <td>{{ $empSal->emps->date_start }}</td>
                    <th>تاريخ التفرغ</th>
                    <td colspan="2">{{ $empSal->emps->date_appoinment }}</td>
                    <td>{{ $empSal->experience_value }}</td>
                </tr>
                <tr>
                    <th>الخبرة الخارجية</th>
                    <td>{{ $empSal->foreigin_value }}</td>
                    <th>الخبرة التطوعية</th>
                    <td>{{ $empSal->voluntary_value }}</td>
                    <th>سنوات الاعتقال</th>
                    <td>{{ $empSal->arrest_value }}</td>
                </tr>
                <tr>
                    <th>المؤهل العلمي </th>
                    <td>{{ $qualification ?? '' }}</td>
                    <th>عدد الزوجات</th>
                    <td>{{ $empSal->no_of_wives }}</td>
                    <th>عدد المعالين</th>
                    <td>{{ $empSal->no_of_dependent }}</td>
                </tr>
            </table>

            <h3 style="background: lightslategrey">البيانات المالية</h3>

            <div style="display: flex; gap: 20px;">
                <table style="border-collapse: collapse; width: 45%;">
                    <tr>
                        <th colspan="2">لاستحقاق</th>
                    </tr>
                    <tr>
                        <th>مربوط الدرجة مع العلاوات</th>
                        <td>{{ number_format($empSal->fixed_salary - $empSal->nature_allowance_value, 2) }}</td>
                    </tr>
                    <tr>
                        <th>علاوة طبيعة العمل (*)</th>
                        <td>{{ number_format($empSal->nature_allowance_value, 2) }}</td>
                    </tr>
                    <tr>
                        <th>الراتب الأساسي </th>
                        <td>{{ number_format($empSal->fixed_salary, 2) }}</td>
                    </tr>
                    <tr>
                        <th>العلاوة الاشرافية (*)</th>
                        <td>{{ $empSal->supervision_value }}</td>
                    </tr>
                    <tr>
                        <th>علاوة الزوجة  ()</th>
                        <td>{{ $empSal->no_of_wives_value }}</td>
                    </tr>
                    <tr>
                        <th>علاوة الاولاد </th>
                        <td>{{ $empSal->no_of_dependent_value }}</td>
                    </tr>
                    <tr>
                        <th>معالحة الراتب حسب النظام</th>
                        <td>{{ number_format($empSal->processing_value, 2) }}</td>
                    </tr>
                    <tr>
                        <th>معامل الجغرافيا</th>
                        <td>1</td>
                    </tr>
                    <tr>
                        <th>إجمالي الراتب المستحق</th>
                        <td>{{ number_format($empSal->full_salary, 2) }}</td>
                    </tr>
                </table>
                <table style="border-collapse: collapse; width: 45%;">
                    <tr>
                        <th colspan="2">الاستقطاع</th>
                    </tr>
                    <tr>
                        <th>تقاعد 10%</th>
                        <td>{{ number_format($empSal->emp_percent_value, 2) }}</td>
                    </tr>
                    <tr>
                        <th>اشتراك 2.5%</th>
                        <td>-</td>
                    </tr>
                    <tr>
                        <th>قسط قرض </th>
                        <td>{{ number_format($empSal->profit, 2) }}</td>
                    </tr>
                    <tr>
                        <th>الخصومات الشهرية</th>
                        <td>{{ $empSal->total_discount }}</td>
                    </tr>
                    <tr>
                        <th>الاجمالي</th>
                        <td>{{ number_format($empSal->emp_percent_value + $empSal->total_discount + $empSal->profit, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="summary">صافي الراتب: <span class="highlight">{{ number_format($empSal->salary, 2) }}</span></div>
            <h3 style="background: lightslategrey">ملاحظات</h3>
            <div style="display: flex; gap: 20px;">
                <table style="border-collapse: collapse; width: 45%;">
                    <tr>
                        <th>الراتب للصرف</th>
                        <td>{{ number_format($empSal->amount_eq , 2) }}</td>
                    </tr>
                    <tr>
                        <th>  حصة الموظف في الإدخار</th>
                        <td>{{ number_format($empSal->total_retirment, 2) }}</td>
                    </tr>
                </table>
                <table style="border-collapse: collapse; width: 45%;">
                    <tr>
                        <th>محول للمستحقات</th>
                        <td>{{ number_format($empSal->saving_amount , 2) }}</td>
                    </tr>
                    <tr>
                        <th>إجمالي المستحقات</th>
                        <td>{{ number_format($empSal->total_saving , 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @if (!$loop->last)
            <div style="page-break-after: always;"></div>
        @endif
    @endforeach
</body>
</html>