@php
    $nameId = $nameId ?? 'name';
    $emailId = $emailId ?? 'email';
    $passwordId = $passwordId ?? 'password';
    $passwordConfirmationId = $passwordConfirmationId ?? 'password_confirmation';
    $initialSuggestion = (function($n, $e) {
        $b = substr(trim($n), 0, 3);
        $b = ucfirst(strtolower($b));
        $l = substr(explode('@', $e)[0], 0, 3);
        $s = ['!','@','#','$','%'][random_int(0, 4)];
        $r = str_pad((string) random_int(0, 99), 2, '0', STR_PAD_LEFT);
        return ($b ?: 'Usu') . $s . ($l ?: 'cor') . $r;
    })('Usuario', 'ejemplo@correo.com');
@endphp
<div id="ps-suggestion" class="mt-2 text-xs text-stone-500 cursor-pointer hover:text-primary transition-colors flex items-center gap-1.5" style="display:none" onclick="psUseSuggestion('{{ $passwordId }}', '{{ $passwordConfirmationId }}', 'ps-text')">
    <span class="material-symbols-outlined text-sm">smart_toy</span>
    <span>Sugerencia: <strong id="ps-text" class="font-mono font-bold text-primary">{{ $initialSuggestion }}</strong></span>
    <span class="text-[10px] text-stone-400 ml-auto">(clic para usar)</span>
</div>
<script>
var ps_prevent_loop = false;
function psUpdate(nameId, emailId, passwordId) {
    var name = document.getElementById(nameId)?.value || '';
    var email = document.getElementById(emailId)?.value || '';
    var local = email.split('@')[0] || '';
    var base = name.substring(0, 3);
    var cap = base.charAt(0).toUpperCase() + base.slice(1).toLowerCase();
    var lbase = local.substring(0, 3);
    var seps = ['!','@','#','$','%'];
    var sep = seps[Math.floor(Math.random() * seps.length)];
    var rand = String(Math.floor(Math.random() * 100)).padStart(2, '0');
    var suggestion = (cap || 'Usu') + sep + (lbase || 'cor') + rand;
    var textEl = document.getElementById('ps-text');
    if (textEl) textEl.textContent = suggestion;
    var container = document.getElementById('ps-suggestion');
    if (container) container.style.display = (name || email) ? 'flex' : 'none';
}
function psUseSuggestion(pwdId, pwdCfmId, textId) {
    var text = document.getElementById(textId)?.textContent;
    if (!text) return;
    var pwd = document.getElementById(pwdId);
    var cfm = document.getElementById(pwdCfmId);
    if (pwd) { pwd.value = text; pwd.dispatchEvent(new Event('input')); }
    if (cfm) { cfm.value = text; cfm.dispatchEvent(new Event('input')); }
}
</script>
