<?php

namespace App\Livewire;

use Livewire\Component;
use Wirement\Vipps\Facades\Vipps;
use Illuminate\Support\Str;

class VippsPaymentComponent extends Component
{
    public $amount = 0;
    public $description = '';
    public $orderId = '';
    public $paymentUrl = '';
    public $status = 'pending';
    public $error = '';

    public function mount($amount = 100, $description = 'Test payment')
    {
        $this->amount = $amount * 100; // Convert to øre
        $this->description = $description;
        $this->orderId = 'order-' . Str::uuid();
    }

    public function createPayment()
    {
        try {
            $this->resetValidation();
            
            $payment = Vipps::createPayment([
                'amount' => $this->amount,
                'currency' => config('vipps.currency', 'NOK'),
                'orderId' => $this->orderId,
                'description' => $this->description,
                'redirectUrl' => route('vipps.callback'),
                'userFlow' => 'WEB_REDIRECT'
            ]);

            $this->paymentUrl = $payment['url'];
            $this->status = 'created';
            
            // Redirect to Vipps payment page
            return redirect()->to($this->paymentUrl);
            
        } catch (\Exception $e) {
            $this->error = 'Failed to create payment: ' . $e->getMessage();
            $this->status = 'error';
        }
    }

    public function checkPaymentStatus()
    {
        try {
            $status = Vipps::getPaymentStatus($this->orderId);
            $this->status = $status['state'] ?? 'unknown';
            
            if ($this->status === 'AUTHORIZED') {
                $this->dispatch('payment-authorized', orderId: $this->orderId);
            } elseif ($this->status === 'CAPTURED') {
                $this->dispatch('payment-completed', orderId: $this->orderId);
            }
            
        } catch (\Exception $e) {
            $this->error = 'Failed to check payment status: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.vipps-payment-component');
    }
}
