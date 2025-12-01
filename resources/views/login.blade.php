@extends('layouts.master')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body p-4 text-center">
                <h4 class="mb-4">تسجيل الدخول</h4>
                <form action="{{ route('login') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="mb-3 position-relative">
                        <input type="text" name="email_prefix" id="emailInput" class="form-control text-center" placeholder="البريد الإلكتروني أو اسم المستخدم" required autocomplete="off">
                        <div id="suggestions" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none;"></div>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control text-center" placeholder="كلمة المرور" required>
                    </div>
                    <button class="btn btn-primary w-100">دخول</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const input = document.getElementById('emailInput');
    const suggestionsBox = document.getElementById('suggestions');

    input.addEventListener('input', async function() {
        const query = this.value;
        if (query.length < 3) {
            suggestionsBox.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/users/search?query=${query}`);
            const emails = await response.json();
            
            suggestionsBox.innerHTML = '';
            if (emails.length > 0) {
                emails.forEach(email => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = email;
                    item.onclick = function() {
                        input.value = email; // Set full email
                        suggestionsBox.style.display = 'none';
                    };
                    suggestionsBox.appendChild(item);
                });
                suggestionsBox.style.display = 'block';
            } else {
                suggestionsBox.style.display = 'none';
            }
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== input && e.target !== suggestionsBox) {
            suggestionsBox.style.display = 'none';
        }
    });
</script>
@endsection
