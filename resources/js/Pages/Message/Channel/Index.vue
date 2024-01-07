<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({ channels: Object, pushServerPublicKey: String })
const page = usePage();

function removeChannel(channel) {
    if (confirm("Are you sure you want to delete?")) {
        router.delete(route('channels.destroy', channel.id), { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout title="Message Channels">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Message Channels
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <table v-if="channels">
                    <tbody>
                        <tr v-for="channel in channels">
                            <td class="pr-5">
                                <Link :href="route('channels.messages.index', channel.id)">{{ channel.name }}</Link>
                            </td>
                            <td v-if="page.props.auth.canEditMessageChannels">
                                <Link :href="route('channels.edit', channel.id)">
                                <SecondaryButton>Edit Channel</SecondaryButton>
                                </Link>
                            </td>
                            <td v-if="page.props.auth.canEditMessageChannels">
                                <DangerButton @click="removeChannel(channel)">Remove Channel</DangerButton>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else> No Message channels for you</p>

                <Link v-if="page.props.auth.canEditMessageChannels" :href="route('channels.create')">
                <PrimaryButton class="mt-5">Create Channel</PrimaryButton>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
