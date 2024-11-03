<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id',
        'student_id',
        'value',
        'type',
        'notes',
    ];

    public function guardian() {
        return $this->belongsTo(Guardian::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public static function createTransaction($data)
    {
        $transaction = self::create($data);
        $transaction->processTransaction(); // Chame o método aqui
        return $transaction;
    }

    public function processTransaction(): void
    {
        $guardian = Guardian::find($this->guardian_id);

        if (!$guardian) {
            return; // Se não encontrar o guardian, não faz nada
        }

        if ($this->type === 'E') {
            $guardian->adicionaSaldo($this->value); // Adiciona saldo para entradas
            $this->processOrders($guardian); // Processa os pedidos após entrada
        } elseif ($this->type === 'S') {
            // Para tipo 'Saída', processa os pedidos
            $this->processOrdersForExit($guardian);
        }
    }

    private function processOrders(Guardian $guardian): void
    {
        $orders = Order::where('student_id', $this->student_id)
            ->where('status', '!=', 'E') // Status que não é 'Pago'
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingValue = $this->value; // Usar o valor da transação

        foreach ($orders as $order) {
            if ($remainingValue <= 0) {
                break; // Para se não houver mais saldo para processar
            }

            // Calcular quanto resta a ser pago
            $amountDue = $order->grand_total - $order->amount_paid;

            if ($amountDue > 0) {
                // Determina o valor a ser pago baseado na wallet e no quanto está devido
                $amountToPay = min($remainingValue, $amountDue); // O que é menor entre o restante da transação e o que está devido

                // Se amountToPay é positivo, atualiza o amount_paid do pedido
                if ($amountToPay > 0) {
                    $newAmountPaid = $order->amount_paid + $amountToPay;

                    // Não permitir que o amount_paid fique negativo
                    if ($newAmountPaid < 0) {
                        $newAmountPaid = 0;
                    }

                    $order->amount_paid = $newAmountPaid; 
                    $remainingValue -= $amountToPay; // Diminui o valor restante da transação

                    // Atualiza o status do pedido se o amount_paid é igual ao grand_total
                    if ($order->amount_paid >= $order->grand_total) {
                        $order->status = 'E'; // Marca o pedido como pago
                    }

                    // Salva o pedido atualizado
                    $order->save();
                }
            }
        }
    }

    private function processOrdersForExit(Guardian $guardian): void
    {
        $currentWallet = $guardian->wallet; // Saldo atual do guardian
        $orders = Order::where('student_id', $this->student_id)
            ->where('status', '!=', 'E') // Status que não é 'Pago'
            ->orderBy('created_at', 'asc')
            ->get();

        $remainingValue = $this->value; // Usar o valor da transação

        foreach ($orders as $order) {
            if ($remainingValue <= 0) {
                break; // Para se não houver mais saldo para processar
            }

            // Calcular quanto resta a ser pago
            $amountDue = $order->grand_total - $order->amount_paid;

            if ($amountDue > 0) {
                // Determina o valor a ser pago baseado na wallet e no quanto está devido
                $amountToPay = min($remainingValue, $amountDue); // O que é menor entre o restante da transação e o que está devido

                // Se o amountToPay for igual ao amountDue, pague o pedido inteiro
                if ($guardian->wallet >= $amountDue) {
                    $guardian->retiraSaldo($amountDue); // Retira da wallet
                    $order->amount_paid += $amountDue; // Atualiza o amount_paid para o total do pedido
                    $remainingValue -= $amountDue; // Diminui o valor restante da transação
                    $order->status = 'E'; // Marca o pedido como pago
                } else {
                    // Se o guardian não tem saldo suficiente, pague o que puder
                    $amountToPay = min($guardian->wallet, $amountDue);
                    $guardian->retiraSaldo($amountToPay); // Retira o valor disponível da wallet
                    $order->amount_paid += $amountToPay; // Atualiza o amount_paid

                    // Verifique se o amount_paid não fica negativo
                    if ($order->amount_paid < 0) {
                        $order->amount_paid = 0;
                    }

                    $remainingValue -= $amountToPay; // Diminui o valor restante da transação
                }

                // Atualiza o status do pedido se o amount_paid é igual ao grand_total
                if ($order->amount_paid >= $order->grand_total) {
                    $order->status = 'E'; // Marca o pedido como pago
                }

                // Salva o pedido atualizado
                $order->save();
            }
        }

        // Após processar todos os pedidos, se ainda houver saldo restante da transação
        if ($remainingValue > 0) {
            // Se o saldo do guardian for positivo, pode ficar negativo
            $guardian->wallet -= $remainingValue; // Subtrai o restante
            $guardian->save();
        }
    }

    public function reversal($reversalType) {
        $this->type = 'R';
        $this->save();

        $guardian = Guardian::find($this->guardian_id);

        if ($guardian){
            if ($reversalType == 'E') {
                $guardian->adicionaSaldo($this->value);
            } else {
                $guardian->retiraSaldo($this->value);
            }
        }
    }
}




