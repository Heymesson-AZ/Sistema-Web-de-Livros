<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nome do usuário -->
        <div>
            <x-input-label for="name" :value="__('Nome')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email  -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- CPF -->
        <div class="mt-4">
            <x-input-label for="cpf" :value="__('CPF')" />
            <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="cpf" :value="old('cpf')" required autocomplete="cpf" />
            <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
        </div>

        <!-- Data de Nascimento -->
        <div class="mt-4">
            <x-input-label for="data_nascimento" :value="__('Data de Nascimento')" />
            <x-text-input id="data_nascimento" class="block mt-1 w-full" type="date" name="data_nascimento" :value="old('data_nascimento')" required autocomplete="data_nascimento" />
            <x-input-error :messages="$errors->get('data_nascimento')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="telefone" :value="__('Telefone')" />
            <x-text-input id="telefone" class="block mt-1 w-full" type="text" name="telefone" :value="old('telefone')" required />
            <x-input-error :messages="$errors->get('telefone')" class="mt-2" />
        </div>
        <!-- senha-->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Senha')" />
            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Senha -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Senha')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Ja e cadastrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Cadastrar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>