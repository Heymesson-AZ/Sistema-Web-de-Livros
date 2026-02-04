{{-- O $slot é uma variável especial para o conteúdo que vai dentro da tag --}}
<div class="alert alert-{{ $tipo }}">
    <div class="alert-title">{{ $titulo }}</div>
    {{ $slot }}
</div>