@php
    if (!function_exists('highlightText')) {
        function highlightText($text, $search) {
            if (!$text) return '';
            $text = (string)$text;
            $search = trim((string)$search);
            
            // Fix encoding issues from CSV imports ensuring it does not return blank string
            $safeText = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            
            if (!$search) return $safeText;
            
            $pattern = '/(' . preg_quote($search, '/') . ')/ui';
            $replaced = preg_replace($pattern, '<mark class="bg-warning text-dark px-1 rounded shadow-sm fw-bold"></mark>', $safeText);
            
            return $replaced !== null ? $replaced : $safeText;
        }
    }
@endphp

@forelse($diagnosticos as $diagnostico)
    <tr>
        <td class="align-middle fw-bold">
            <span class="badge bg-secondary px-3 py-2 text-white shadow-sm"><i class="fas fa-hashtag me-1"></i>{!! highlightText($diagnostico->codigo, $search ?? '') !!}</span>
        </td>
        <td class="align-middle fs-6">
            {!! highlightText($diagnostico->nombre, $search ?? '') !!}
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
