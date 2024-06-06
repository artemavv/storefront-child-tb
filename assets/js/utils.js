export const storage = (key, data = null) => {
    if (!data) {
        return JSON.parse(localStorage.getItem(key))
    }
    localStorage.setItem(key, JSON.stringify(data))
}

export const dateDifference = (date1, date2) => {
    const diff = Math.floor(date1.getTime() - date2.getTime());
    const day = 1000 * 60 * 60 * 24;

    return Math.floor(diff / day);
}

export function substringOut(value, number) {
    if (value.length > number) {
        return `${value.substring(0, number)}...`
    } else {
        return value
    }
}
