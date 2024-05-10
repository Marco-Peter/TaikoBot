<x-layout>
    <form action="/register" method="post">
        @csrf

        <div>
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" required autofocus autocomplete="given-name" />
        </div>

        <div>
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" required autofocus autocomplete="family-name" />
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autofocus autocomplete="username" />
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required autocomplete="new-password" />
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" />
        </div>

        <div>
            <label for="terms">I agree to the terms of service and privacy policy</label>
            <input type="checkbox" name="terms" id="terms">
        </div>

        <div>
            <a href="/login">Log in</a>
        </div>

        <button type="submit">Register</button>
    </form>
</x-layout>
