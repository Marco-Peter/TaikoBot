<x-layout>
    <div>
        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
    </div>

    <form action="/forgot-password" method="post">
        @csrf

        <div>
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required autofocus autocomplete="name" />
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required autofocus autocomplete="username" />
        </div>

        <button type="submit">Email password reset link</button>
    </form>
</x-layout>
