<x-layout>
    <nav class="navBar">
        <div><img src="/images/taikobot-logo-white.png" alt="TaikoBot" height="60px" id="tb-logo"></div>
    </nav>

    <main>
        <h1 class="navBar pageTitle">Login</h1>
        <form action="/login" method="post" class="login-form">
            @csrf

            <div class="table">
                <div class="table-row">
                    <label for="email" class="table-cell">Email</label>
                    <input type="email" name="email" id="email" required autofocus autocomplete="username"
                        class="table-cell" />
                </div>
                <div class="table-row">
                    <label for="password" class="table-cell">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        class="table-cell" />
                </div>
            </div>

            <label for="remember_me">Remember me</label>
            <input type="checkbox" name="remember" id="remember_me">

            <p><a href="/forgot-password">Forgot password</a></p>
            <p><a href="/register">Register</a></p>
            <p><button type="submit">Log in</button></p>
        </form>
    </main>
</x-layout>
