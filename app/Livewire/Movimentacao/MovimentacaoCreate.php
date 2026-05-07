<?php

namespace App\Livewire\Movimentacao;

use App\Models\movimentacao;
use App\Models\Produto;
use Illuminate\Console\View\Components\Component as ComponentsComponent;
use Livewire\Component;

class MovimentacaoCreate extends Component
{
    public $produtos;
    public $idProdutoSelecionado;
    public $tipo = 'saida';
    public $quantidade_movimentada;
    public $data_movimentacao;
    public $idUsuario;
    public $alertaEstoqueBaixo;
    public $movimentacaos;

    public function mount()
    {
        $this->produtos = Produto::orderby('nome')->get();
        $this->data_movimentacao = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.movimentacao.movimentacao-create');
    }

    public function store()
    {
        $produto = Produto::find($this->idProdutoSelecionado);

        if (
            $produto->qtd_estoque < $this->quantidade_movimentada
            && $this->tipo == 'saida'
        ) {
            $this->addError('quantidade_movimentada', 'Quantidade em
             estoque insuficiente');
        }

        //atualizar estoque
        if ($this->tipo == "entrada") {
            // $produto->qtd_estoque = $produto->qtd_estoque + $this->quantidade_movimentada;
            //$produto->qtd_estoque =+ $this->quantidade_movimentada;
            $produto->increment('qtd_estoque', $this->quantidade_movimentada);
        } else {
            // $produto->qtd_estoque = $produto->qtd_estoque - $this->quantidade_movimentada;
            //$produto->qtd_estoque -= $this->quantidade_movimentada;
            $produto->decrement('qtd_estoque', $this->quantidade_movimentada);
        }

        //registrar/criar movimentacao
        Movimentacao::create([
            'quantidade' => $this->quantidade_movimentada,
            'data_movimentacao' => $this->data_movimentacao,
            'tipo' => $this->tipo,
            'produto_id' => $this->idProdutoSelecionado,
            'user_id' => 1

        ]);
        $produto->update();
        // verificar estoque baixo

        $produto->refresh();
        if ($produto->qtd_estoque < $produto->qtd_minima) {
            $this->alertaEstoqueBaixo = "ALERTA: Estoque Baixo para
            ($produto->nome). Quantidade atual: ($produto->qtd_estoque)";
        } else {
            $this->alertaEstoqueBaixo = "";
        }

        session()->flash('message', 'Movimentação registrada com sucesso!');
        $this->reset(['quantidade_movimentada', 'tipo']);
        $this->produtos = Produto::orderby('nome')->get();
    }
}
