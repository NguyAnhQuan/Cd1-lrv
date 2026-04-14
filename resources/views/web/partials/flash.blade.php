@if(session('success'))
    <div class="ft-wrap"><div class="ft-alert ft-alert--success">{{ session('success') }}</div></div>
@endif
@if(session('error'))
    <div class="ft-wrap"><div class="ft-alert ft-alert--error">{{ session('error') }}</div></div>
@endif
