<?php
function tempoDecorrido($data_envio) {
    $agora = new DateTime();
    $data = new DateTime($data_envio);
    $diferenca = $agora->diff($data);

    if ($diferenca->y > 0) {
        return 'há ' . $diferenca->y . ' ano' . ($diferenca->y > 1 ? 's' : '');
    } elseif ($diferenca->m > 0) {
        return 'há ' . $diferenca->m . ' mês' . ($diferenca->m > 1 ? 'es' : '');
    } elseif ($diferenca->d > 7) {
        return 'há ' . floor($diferenca->d / 7) . ' semana' . (floor($diferenca->d / 7) > 1 ? 's' : '');
    } elseif ($diferenca->d > 0) {
        return 'há ' . $diferenca->d . ' dia' . ($diferenca->d > 1 ? 's' : '');
    } elseif ($diferenca->h > 0) {
        return 'há ' . $diferenca->h . ' hora' . ($diferenca->h > 1 ? 's' : '');
    } elseif ($diferenca->i > 0) {
        return 'há ' . $diferenca->i . ' minuto' . ($diferenca->i > 1 ? 's' : '');
    } else {
        return 'agora mesmo';
    }
}