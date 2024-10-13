<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Registro" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>
        <div class="pb-3 font-semibold text-center text-gray-800 uppercase">
            Consultalo con el Administrador
        </div>
        <div class="pb-5 text-sm text-center text-gray-600">
            ¿Ya tienes una cuenta?
            <Link
                :href="route('login')"
                class="font-semibold text-gray-800 hover:text-[#2EBAA1] hover:underline"
            >
                Inicia sesión aquí
            </Link>
        </div>
        <div class="pb-3 font-semibold text-center text-sm text-gray-800">
            Lo siento pero por motivos de seguridad solo el administrador puede crearte una cuenta nueva!
        </div>
    </AuthenticationCard>
</template>
