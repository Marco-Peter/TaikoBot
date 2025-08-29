export function formatDate(t) {
    return new Date(t).toISOString().slice(0, 10).split("-").reverse().join(".");
}

export function formatTime(t) {
    return new Date(t).toISOString().slice(11, 16);
}
