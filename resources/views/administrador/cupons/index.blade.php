<x-layouts.principal title="Gestão de Cupons - Universo de Papel">
    <div class="container py-4">

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <i data-lucide="ticket" class="text-primary"></i>
                    <span>Cupons de Desconto</span>
                </h1>
                <p class="text-muted small mb-0">Gerencie os cupons promocionais para incentivar vendas na plataforma.</p>
            </div>
            <a href="{{ route('admin.cupons.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center gap-1">
                <i data-lucide="plus-circle" style="width: 16px; height: 16px;"></i>
                <span>Novo Cupom</span>
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('status') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- Tabela de Cupons -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Código</th>
                                <th>Tipo</th>
                                <th>Desconto</th>
                                <th>Usos</th>
                                <th>Validade</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cupons as $cupom)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold font-monospace text-primary fs-6">{{ $cupom->codigo }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill text-capitalize">
                                            {{ $cupom->tipo_desconto === 'percentual' ? 'Percentual' : 'Valor Fixo' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $cupom->descricao_desconto }}</span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">
                                            {{ $cupom->pedidos_count ?? $cupom->usos_atuais }} / {{ $cupom->limite_uso ?? '∞' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">
                                            {{ $cupom->validade_cupom ? $cupom->validade_cupom->format('d/m/Y') : 'Sem expiração' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($cupom->isValido())
                                            <span class="badge bg-success-subtle text-success rounded-pill">Ativo</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger rounded-pill">Inativo / Expirado</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            <!-- BOTÃO DE EDIÇÃO PADRONIZADO (ICON-ONLY EM DESTAQUE) -->
                                            <a href="{{ route('admin.cupons.edit', $cupom) }}"
                                                class="btn btn-sm btn-primary rounded-3 text-white fw-semibold shadow-sm px-2.5 py-1 d-inline-flex align-items-center gap-1"
                                                title="Editar Cupom">
                                                <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                                                <span>Editar</span>
                                                class="btn btn-sm btn-primary rounded-3 text-white shadow-sm d-inline-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px;"
                                                title="Editar Cupom" aria-label="Editar">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </a>
                                            <form action="{{ route('admin.cupons.destroy', $cupom) }}" method="POST"
                                                data-confirm="Deseja realmente excluir o cupom {{ $cupom->codigo }}?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-1 px-2" title="Excluir cupom">
                                                    <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger rounded-3 d-inline-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px;"
                                                    title="Excluir cupom" aria-label="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i data-lucide="ticket" class="d-block mx-auto mb-2 text-secondary" style="width: 36px; height: 36px;"></i>
                                        Nenhum cupom cadastrado até o momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($cupons->hasPages())
                <div class="card-footer bg-white py-3 border-top">
                    {{ $cupons->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.principal>

