<x-layout>
    <nav class="navBar">
        <div><img src="/images/taikobot-logo-white.png" alt="TaikoBot" height="60px" id="tb-logo"></div>
    </nav>

    <main>
        <p>Forgot your password? No problem. Just let us know your email address and we will email you a password reset
            link.</p>

        <form action="/forgot-password" method="post" class="register-form">
            @csrf

            <div class="table">
                <div class="table-row">
                    <label for="name" class="table-cell">Name</label>
                    <input type="text" id="name" name="name" required autofocus autocomplete="name"
                        class="table-cell" />
                </div>

                <div class="table-row">
                    <label for="email" class="table-cell">Email</label>
                    <input type="email" name="email" id="email" required autofocus autocomplete="username"
                        class="table-cell" />
                </div>
            </div>

            <p><a href="/login">Log in</a></p>
            <p><button type="submit">Email password reset link</button></p>
        </form>
    </main>
</x-layout>
