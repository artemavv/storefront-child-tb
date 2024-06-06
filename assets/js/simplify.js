// AlS - Alchemist Simplify
// Файл импортируется в app.js глобально, на window.
// VS Code это не видит и поэтому подсказок не выдаёт, но в браузере всё работает.

export const qs = (el, className) => { return el.querySelector(className); };
export const qsAll = (el, className) => { return el.querySelectorAll(className); };
export const hasClass = (el, className) => { return el.classList.contains(className); };
export const addClass = (el, className) => { return el.classList.add(className); };
export const delClass = (el, className) => { return el.classList.remove(className); };