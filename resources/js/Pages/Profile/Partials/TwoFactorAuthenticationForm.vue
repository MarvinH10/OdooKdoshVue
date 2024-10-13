<script setup>
import { ref, computed, watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import ActionSection from "@/Components/ActionSection.vue";
import ConfirmsPassword from "@/Components/ConfirmsPassword.vue";
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    requiresConfirmation: Boolean,
});

const page = usePage();
const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCode = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);

const confirmationForm = useForm({
    code: "",
});

const twoFactorEnabled = computed(
    () => !enabling.value && page.props.auth.user?.two_factor_enabled
);

watch(twoFactorEnabled, () => {
    if (!twoFactorEnabled.value) {
        confirmationForm.reset();
        confirmationForm.clearErrors();
    }
});

const enableTwoFactorAuthentication = () => {
    enabling.value = true;

    router.post(
        route("two-factor.enable"),
        {},
        {
            preserveScroll: true,
            onSuccess: () =>
                Promise.all([
                    showQrCode(),
                    showSetupKey(),
                    showRecoveryCodes(),
                ]),
            onFinish: () => {
                enabling.value = false;
                confirming.value = props.requiresConfirmation;
            },
        }
    );
};

const showQrCode = () => {
    return axios.get(route("two-factor.qr-code")).then((response) => {
        qrCode.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get(route("two-factor.secret-key")).then((response) => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get(route("two-factor.recovery-codes")).then((response) => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactorAuthentication = () => {
    confirmationForm.post(route("two-factor.confirm"), {
        errorBag: "confirmTwoFactorAuthentication",
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            confirming.value = false;
            qrCode.value = null;
            setupKey.value = null;
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios
        .post(route("two-factor.recovery-codes"))
        .then(() => showRecoveryCodes());
};

const disableTwoFactorAuthentication = () => {
    disabling.value = true;

    router.delete(route("two-factor.disable"), {
        preserveScroll: true,
        onSuccess: () => {
            disabling.value = false;
            confirming.value = false;
        },
    });
};
</script>

<template>
    <ActionSection>
        <template #title> Autenticación de dos factores </template>

        <template #description>
            Agregue seguridad adicional a su cuenta utilizando la autenticación
            de dos factores.
        </template>

        <template #content>
            <h3
                v-if="twoFactorEnabled && !confirming"
                class="text-lg font-medium text-gray-900"
            >
                Ha habilitado la autenticación de dos factores.
            </h3>

            <h3
                v-else-if="twoFactorEnabled && confirming"
                class="text-lg font-medium text-gray-900"
            >
                Termine de habilitar la autenticación de dos factores.
            </h3>

            <h3 v-else class="text-lg font-medium text-gray-900">
                No ha habilitado la autenticación de dos factores.
            </h3>

            <div class="mt-3 max-w-xl text-sm text-gray-600">
                <p>
                    Cuando la autenticación de dos factores está habilitada, se
                    le solicitará un token aleatorio seguro durante la
                    autenticación. Puede recuperar este token desde la
                    aplicación Google Authenticator de su teléfono.
                </p>
            </div>

            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm text-gray-600">
                        <p v-if="confirming" class="font-semibold">
                            Para terminar de habilitar la autenticación de dos
                            factores, escanee el siguiente código QR usando la
                            aplicación de autenticación de su teléfono o ingrese
                            la clave de configuración y proporcione el código
                            OTP generado.
                        </p>

                        <p v-else>
                            La autenticación de dos factores ahora está
                            habilitada. Escanee el siguiente código QR usando la
                            aplicación de autenticación de su teléfono o ingrese
                            la clave de configuración.
                        </p>
                    </div>

                    <div
                        class="mt-4 p-2 inline-block bg-white"
                        v-html="qrCode"
                    />

                    <div
                        v-if="setupKey"
                        class="mt-4 max-w-xl text-sm text-gray-600"
                    >
                        <p class="font-semibold">
                            Clave de configuración:
                            <span v-html="setupKey"></span>
                        </p>
                    </div>

                    <div v-if="confirming" class="mt-4">
                        <InputLabel for="code" value="Code" />

                        <TextInput
                            id="code"
                            v-model="confirmationForm.code"
                            type="text"
                            name="code"
                            class="block mt-1 w-1/2"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                            @keyup.enter="confirmTwoFactorAuthentication"
                        />

                        <InputError
                            :message="confirmationForm.errors.code"
                            class="mt-2"
                        />
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0 && !confirming">
                    <div class="mt-4 max-w-xl text-sm text-gray-600">
                        <p class="font-semibold">
                            Guarde estos códigos de recuperación en un
                            administrador de contraseñas seguro. Se pueden
                            utilizar para recuperar el acceso a su cuenta si
                            pierde su dispositivo de autenticación de dos
                            factores.
                        </p>
                    </div>

                    <div
                        class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-gray-100 rounded-lg"
                    >
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="!twoFactorEnabled">
                    <ConfirmsPassword
                        @confirmed="enableTwoFactorAuthentication"
                    >
                        <PrimaryButton
                            type="button"
                            :class="{ 'opacity-25': enabling }"
                            :disabled="enabling"
                        >
                            Habilitar
                        </PrimaryButton>
                    </ConfirmsPassword>
                </div>

                <div v-else>
                    <ConfirmsPassword
                        @confirmed="confirmTwoFactorAuthentication"
                    >
                        <PrimaryButton
                            v-if="confirming"
                            type="button"
                            class="me-3"
                            :class="{ 'opacity-25': enabling }"
                            :disabled="enabling"
                        >
                            Confirm
                        </PrimaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <SecondaryButton
                            v-if="recoveryCodes.length > 0 && !confirming"
                            class="me-3"
                        >
                            Regenerate Recovery Codes
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <SecondaryButton
                            v-if="recoveryCodes.length === 0 && !confirming"
                            class="me-3"
                        >
                            Show Recovery Codes
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword
                        @confirmed="disableTwoFactorAuthentication"
                    >
                        <SecondaryButton
                            v-if="confirming"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                        >
                            Cancel
                        </SecondaryButton>
                    </ConfirmsPassword>

                    <ConfirmsPassword
                        @confirmed="disableTwoFactorAuthentication"
                    >
                        <DangerButton
                            v-if="!confirming"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                        >
                            Disable
                        </DangerButton>
                    </ConfirmsPassword>
                </div>
            </div>
        </template>
    </ActionSection>
</template>
