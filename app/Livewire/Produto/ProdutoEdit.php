<?php

namespace App\Livewire\Produto;

use App\Models\Produto;
use Livewire\Component;

class ProdutoEdit extends Component
{
    public $nome;
    public $valor;
    public $qtd_estoque;
    public $qtd_minima;
    public $produtoId;

    public function mount($id)
    {
        $produto = Produto::find($id);

        if ($produto == null) {
            session()->flash('error', 'Não encontado');
            return redirect()->route('produto.index');
        }

        $this->produtoId = $produto->id;
        $this->nome = $produto->nome;
        $this->valor = $produto->valor;
        $this->qtd_estoque = $produto->qtd_estoque;
        $this->qtd_minima = $produto->qtd_minima;
    }

    public function update()
    {
        $produto = Produto::find($this->produtoId);

         if ($produto == null) {
            session()->flash('error', 'Não encontado');
            return redirect()->route('produto.index');
        }

        $produto->nome = $this->nome;
        $produto->valor= $this->valor;
        $produto->qtd_estoque= $this->qtd_estoque;
        $produto->qtd_minima = $this->qtd_minima;

        $produto->save();

        session()->flash('success', 'Atualizado');
        return redirect()->route('produto.index');
    }

    public function render()
    {
        return view('livewire.produto.produto-edit');
    }
}
