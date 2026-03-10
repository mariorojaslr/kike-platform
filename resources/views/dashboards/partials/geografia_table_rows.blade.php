@forelse($localidades as $loc)
<tr>
    <td>
        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">
            {{ $loc->provincia->nombre ?? 'N/A' }}
        </span>
    </td>
    <td><strong>{{ $loc->nombre }}</strong></td>
    <td class="text-end">
        <form action="{{ route('geografia.localidad.destroy', $loc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro quieres eliminar esta localidad geográfica del padrón?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-light text-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="3" class="text-center text-muted py-4">
        <i class="fas fa-search fa-2x mb-3 text-light"></i>
        <p>No se encontraron localidades con ese criterio de búsqueda.</p>
    </td>
</tr>
@endforelse
