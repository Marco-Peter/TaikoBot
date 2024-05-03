<x-layout>
    <form action="/login" method="post">
        @csrf

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autofocus autocomplete="username" />
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required autocomplete="current-password" />
        </div>

        <div>
            <label for="remember_me">Remember me</label>
            <input type="checkbox" name="remember" id="remember_me">
        </div>

        <div>
            <a href="/forgot-password">Forgot password</a>
        </div>

        <div>
            <a href="/register">Register</a>
        </div>

        <button type="submit">Log in</button>
    </form>
</x-layout>
