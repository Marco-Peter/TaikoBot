<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';
import Checkbox from '@/components/ui/checkbox/Checkbox.vue';
import Sheet from '@/components/ui/sheet/Sheet.vue';
import SheetTrigger from '@/components/ui/sheet/SheetTrigger.vue';
import SheetContent from '@/components/ui/sheet/SheetContent.vue';
import SheetDescription from '@/components/ui/sheet/SheetDescription.vue';
import ScrollArea from '@/components/ui/scroll-area/ScrollArea.vue';
import SheetHeader from '@/components/ui/sheet/SheetHeader.vue';
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue';

const props = defineProps<{
    passwordRules: string;
    terms: string;
    policy: string;
}>();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});
</script>

<template>

    <Head title="Register" />

    <Form v-bind="store.form()" :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="first_name">First Name</Label>
                <Input id="first_name" type="text" required autofocus :tabindex="1" autocomplete="first name"
                    name="first_name" placeholder="First name" />
                <InputError :message="errors.first_name" />
            </div>
            <div class="grid gap-2">
                <Label for="name">Last Name</Label>
                <Input id="last_name" type="text" required :tabindex="2" autocomplete="last name" name="last_name"
                    placeholder="Last name" />
                <InputError :message="errors.last_name" />
            </div>
            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input id="email" type="email" required :tabindex="3" autocomplete="email" name="email"
                    placeholder="email@example.com" />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput id="password" required :tabindex="4" autocomplete="new-password" name="password"
                    placeholder="Password" :passwordrules="passwordRules" />
                <InputError :message="errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput id="password_confirmation" required :tabindex="5" autocomplete="new-password"
                    name="password_confirmation" placeholder="Confirm password" :passwordrules="passwordRules" />
                <InputError :message="errors.password_confirmation" />
            </div>

            <div class="grid gap-2">
                <div class="flex gap-6">
                    <Checkbox id="terms" name="terms" required />
                    <Label for="terms">I agree to the Terms of service and Member Expectations.</Label>
                </div>
                <InputError :message="errors.terms" />
                <Sheet>
                    <SheetTrigger><b><u>Terms of Service</u></b></SheetTrigger>
                    <SheetContent side="bottom">
                        <SheetHeader>
                            <SheetTitle>Terms of Service</SheetTitle>
                        </SheetHeader>
                        <ScrollArea class="h-3/4 w-9/10" v-html="props.terms">

                        </ScrollArea>
                    </SheetContent>
                </Sheet>
                <Sheet>
                    <SheetTrigger><b><u>Member Expectations</u></b></SheetTrigger>
                    <SheetContent side="bottom">
                        <SheetHeader>
                            <SheetTitle>Member Expectations</SheetTitle>
                        </SheetHeader>
                        <ScrollArea class="h-3/4 w-9/10" v-html="props.policy"></ScrollArea>
                    </SheetContent>
                </Sheet>
            </div>

            <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="processing"
                data-test="register-user-button">
                <Spinner v-if="processing" />
                Create account
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <TextLink :href="login()" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
        </div>
    </Form>
</template>
