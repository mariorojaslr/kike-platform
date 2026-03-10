@forelse($diagnosticos as $diagnostico)
    <tr>
        <td class="align-middle fw-bold">
            <span class="badge bg-secondary px-3 py-2 text-white shadow-sm"><i class="fas fa-hashtag me-1"></i>{{ $diagnostico->codigo }}</span>
        </td>
        <td class="align-middle fs-6">
            {{ $diagnostico->nombre }}
        </td>
        <td class="align-middle">
            <span class="badge bg-success-subtle text-success border border-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>Activo / Aprobado</span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="text-center py-5 text-muted">
            <i class="fas fa-search-minus fa-3x mb-3 text-light"></i><br>
            <h5 class="fw-bold">No hay Patologías en el Catálogo</h5>
        </td>
    </tr>
@endforelse
