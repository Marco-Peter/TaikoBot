<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import { register } from '@/routes';
import AppLogo from '@/components/AppLogo.vue';
import Table from '@/components/ui/table/Table.vue';
import TableHeader from '@/components/ui/table/TableHeader.vue';
import TableRow from '@/components/ui/table/TableRow.vue';
import TableHead from '@/components/ui/table/TableHead.vue';
import TableBody from '@/components/ui/table/TableBody.vue';
import TableCell from '@/components/ui/table/TableCell.vue';
</script>

<template>
    <Head title="Welcome">
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]"
    >
        <header
            class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl"
        >
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="register()"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0"
        >
            <main
                class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row"
            >
                <div
                    class="flex-1 rounded-br-lg rounded-bl-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] lg:rounded-tl-lg lg:rounded-br-none lg:p-20 dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d]"
                >
                    <AppLogo />
                    <h1 class="mt-5 mb-1">Möchtest du Taiko ausprobieren? Komm zu uns!</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        Zweimal im Jahr, jeweils vor Beginn des Taikosemesters, laden wir Interessierte in unser Dojo ein, um Taiko auszuprobieren.
                        Dieser Schnupperkurs besteht aus zwei zweistündigen Workshops, die sich über zwei Wochen erstrecken. Mit unseren
                        spielerischen und sportlichen Methoden vermitteln wir dir die grundlegende Technik, Fertigkeiten und Etikette des Taikospiels.
                        Nachdem du beide Workshops erfolgreich abgeschlossen hast, hast du in der darauf folgenden Woche die Möglichkeit,
                        dich für unser wöchentliches Grundlagen-Freitagstraining einzuschreiben.<br>
                        Weitere Einzelheiten werden dir nach deiner Registrierung per E-Mail mitgeteilt.
                    </p>
                    <h1>Unsere nächsten öffentlichen Trainings</h1>
                    <!-- TODO: Generate this information from database!!!-->
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Datum</TableHead>
                                <TableHead>Kurs</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow>
                                <TableCell>
                                    4.9.2026<br>11.9.2026
                                </TableCell>
                                <TableCell>Schnupperkurs</TableCell>
                            </TableRow>
                            <TableRow>
                                <TableCell>
                                    19.2.2027<br>26.2.2027
                                </TableCell>
                                <TableCell>Schnupperkurs</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
