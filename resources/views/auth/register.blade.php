<x-layout>
    <nav class="navBar">
        <div><img src="/images/taikobot-logo-white.png" alt="TaikoBot" height="60px" id="tb-logo"></div>
    </nav>

    <main>
        <h1 class="navBar pageTitle">Register</h1>
        <p>New to Taiko Zürich? Wonderful! Please register here and we will contact you when we
            will host our "Schnupperlektionen" (usually in February and September).
        </p>
        <form action="/register" method="post" class="register-form">
            @csrf

            <div class="table">
                <div class="table-row">
                    <label for="first_name" class="table-cell">First Name</label>
                    <input type="text" id="first_name" name="first_name" required autofocus autocomplete="given-name"
                        class="table-cell" />
                </div>

                <div class="table-row">
                    <label for="last_name" class="table-cell">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required autofocus autocomplete="family-name"
                        class="table-cell" />
                </div>

                <div class="table-row">
                    <label for="email" class="table-cell">Email</label>
                    <input type="email" name="email" id="email" required autofocus autocomplete="username"
                        class="table-cell" />
                </div>

                <div class="table-row">
                    <label for="password" class="table-cell">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                        class="table-cell" />
                </div>

                <div class="table-row">
                    <label for="password_confirmation" class="table-cell">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        autocomplete="new-password" class="table-cell" />
                </div>
            </div>

            <p><label for="terms">I agree to the terms of service and privacy policy</label>
            <input type="checkbox" name="terms" id="terms"></p>

            <p><a href="/login">Log in</a></p>
            <p><button type="submit">Register</button></p>
        </form>
    </main>
</x-layout>
