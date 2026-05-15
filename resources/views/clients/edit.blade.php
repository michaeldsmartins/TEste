@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Editar Cliente</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nome do Cliente</label>
                            <input type="text" name="name" class="form-control" value="{{ $client->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" value="{{ $client->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="phone" class="form-control" value="{{ $client->phone }}">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Atualizar Cliente</button>
                            <a href="{{ route('clients.index') }}" class="btn btn-link text-muted">Voltar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
