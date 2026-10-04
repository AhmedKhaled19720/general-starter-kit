function toDate(value: string | Date | null | undefined): Date | null {
    if (!value) {
        return null;
    }

    const date = typeof value === 'string' ? new Date(value) : value;

    return Number.isNaN(date.getTime()) ? null : date;
}

export function formatDate(value: string | Date | null | undefined): string {
    const date = toDate(value);

    if (!date) {
        return '—';
    }

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
}

export function formatDateTime(
    value: string | Date | null | undefined,
): string {
    const date = toDate(value);

    if (!date) {
        return '—';
    }

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${formatDate(date)} ${hours}:${minutes}`;
}

export function toDateTimeLocal(
    value: string | Date | null | undefined,
): string {
    const date = toDate(value);

    if (!date) {
        return '';
    }

    return formatDateTime(date).replace(' ', 'T');
}
