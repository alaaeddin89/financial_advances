<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\FinancialAdvance;

class NewAdvancePendingConfirmation extends Notification implements ShouldQueue // استخدام ShouldQueue لتحسين الأداء
{
    use Queueable;

    protected $advance;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(FinancialAdvance $advance)
    {
        $this->advance = $advance;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // سنستخدم قناة قاعدة البيانات لعرض الإشعار في لوحة الكاشير
        return ['database'];
    }

    /**
     * الحصول على تمثيل الإشعار المخصص للتخزين في قاعدة البيانات.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'advance_id' => $this->advance->id,
            'amount' => $this->advance->amount,
            'issuer_name' => auth()->user()->full_name ?? 'النظام', // اسم منشئ العهدة
            'message' => 'لديك عهدة مالية جديدة برقم **' . $this->advance->id . '** بقيمة ' . number_format($this->advance->amount, 2) . ' في انتظار القبول.',
            'link' => route('advances.show',$this->advance->id),
           
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
