const MONTHS = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
const WEEKDAYS = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
const WEEKDAY_NAMES = ['شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه'];
const PERSIAN_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
const ARABIC_DIGITS = '٠١٢٣٤٥٦٧٨٩';

const formatter = new Intl.DateTimeFormat('en-US-u-ca-persian-nu-latn', {
    timeZone: 'UTC',
    year: 'numeric',
    month: 'numeric',
    day: 'numeric',
});

const pad = (value) => String(value).padStart(2, '0');
const toFa = (value) => String(value).replace(/\d/g, (digit) => PERSIAN_DIGITS[digit]);
const toLatin = (value) =>
    String(value).replace(/[۰-۹٠-٩]/g, (char) => {
        const index = PERSIAN_DIGITS.indexOf(char);

        return String(index !== -1 ? index : ARABIC_DIGITS.indexOf(char));
    });

function toJalali(gy, gm, gd) {
    const parts = formatter.formatToParts(new Date(Date.UTC(gy, gm - 1, gd, 12)));
    const pick = (type) => Number(parts.find((part) => part.type === type).value);

    return [pick('year'), pick('month'), pick('day')];
}

function compare(a, b) {
    return a[0] - b[0] || a[1] - b[1] || a[2] - b[2];
}

function toGregorian(jy, jm, jd) {
    const offset = jm <= 6 ? (jm - 1) * 31 : 186 + (jm - 7) * 30;
    let date = new Date(Date.UTC(jy + 621, 2, 21 + offset + jd - 1, 12));

    for (let step = 0; step < 5; step++) {
        const current = toJalali(date.getUTCFullYear(), date.getUTCMonth() + 1, date.getUTCDate());
        const diff = compare(current, [jy, jm, jd]);

        if (diff === 0) {
            break;
        }

        date = new Date(date.getTime() + (diff > 0 ? -86400000 : 86400000));
    }

    return [date.getUTCFullYear(), date.getUTCMonth() + 1, date.getUTCDate()];
}

function monthLength(jy, jm) {
    const next = jm === 12 ? [jy + 1, 1] : [jy, jm + 1];
    const start = Date.UTC(...toGregorian(jy, jm, 1).map((value, index) => (index === 1 ? value - 1 : value)), 12);
    const end = Date.UTC(...toGregorian(next[0], next[1], 1).map((value, index) => (index === 1 ? value - 1 : value)), 12);

    return Math.round((end - start) / 86400000);
}

function weekdayColumn(jy, jm, jd) {
    const [gy, gm, gd] = toGregorian(jy, jm, jd);

    return (new Date(Date.UTC(gy, gm - 1, gd, 12)).getUTCDay() + 1) % 7;
}

function parseIso(iso) {
    const match = /^(\d{4})-(\d{2})-(\d{2})(?:[T ](\d{2}):(\d{2}))?/.exec(iso || '');

    return match ? { g: [Number(match[1]), Number(match[2]), Number(match[3])], time: match[4] ? `${match[4]}:${match[5]}` : '00:00' } : null;
}

const MONTH_SHORT = MONTHS;

export function jalaliPicker({ value = '', withTime = false, group = null, role = null, minYear = 1300, maxYear = null } = {}) {
    return {
        withTime,
        group,
        role,
        iso: '',
        text: '',
        time: '00:00',
        open: false,
        mode: 'days',
        viewYear: 1400,
        viewMonth: 1,
        yearPage: 1400,
        months: MONTH_SHORT,
        weekdays: WEEKDAYS,
        peer: null,

        init() {
            const parsed = parseIso(value);
            const today = toJalali(...(parsed ? parsed.g : this.todayGregorian()));

            this.viewYear = today[0];
            this.viewMonth = today[1];

            if (parsed) {
                this.time = parsed.time;
                this.commit(toJalali(...parsed.g), false);
            }

            if (this.group) {
                window.addEventListener('jalali-range', (event) => {
                    if (event.detail.group === this.group && event.detail.role !== this.role) {
                        this.peer = event.detail.date;
                    }
                });

                this.$nextTick(() => this.broadcast());
            }
        },

        get upperYear() {
            return maxYear ?? toJalali(...this.todayGregorian())[0] + 10;
        },

        todayGregorian() {
            const now = new Date();

            return [now.getFullYear(), now.getMonth() + 1, now.getDate()];
        },

        selected() {
            const parsed = parseIso(this.iso);

            return parsed ? toJalali(...parsed.g) : null;
        },

        broadcast() {
            window.dispatchEvent(new CustomEvent('jalali-range', { detail: { group: this.group, role: this.role, date: this.selected() } }));
        },

        commit(jalali, notify = true) {
            const [gy, gm, gd] = toGregorian(...jalali);

            this.iso = `${gy}-${pad(gm)}-${pad(gd)}` + (this.withTime ? `T${this.time || '00:00'}` : '');
            this.text = toFa(`${jalali[0]}/${pad(jalali[1])}/${pad(jalali[2])}`);

            if (notify && this.group) {
                this.broadcast();
            }
        },

        toggle() {
            this.open = ! this.open;

            if (this.open) {
                this.mode = 'days';
                this.focusView();
            }
        },

        focusView() {
            const anchor = this.selected() ?? (this.role === 'end' ? this.peer : null);

            if (anchor) {
                this.viewYear = anchor[0];
                this.viewMonth = anchor[1];
            }
        },

        clear() {
            this.iso = '';
            this.text = '';
            this.open = false;

            if (this.group) {
                this.broadcast();
            }
        },

        isDisabled(day) {
            const date = [this.viewYear, this.viewMonth, day];

            if (this.role === 'end' && this.peer) {
                return compare(date, this.peer) < 0;
            }

            if (this.role === 'start' && this.peer) {
                return compare(date, this.peer) > 0;
            }

            return false;
        },

        pick(day) {
            if (this.isDisabled(day)) {
                return;
            }

            this.commit([this.viewYear, this.viewMonth, day]);
            this.open = false;
        },

        pickToday() {
            const today = toJalali(...this.todayGregorian());

            this.viewYear = today[0];
            this.viewMonth = today[1];
            this.commit(today);
            this.open = false;
        },

        updateTime() {
            const current = this.selected();

            if (current) {
                this.commit(current);
            }
        },

        parseTyped() {
            const match = /^(\d{3,4})[/\-.](\d{1,2})[/\-.](\d{1,2})$/.exec(toLatin(this.text).trim());

            if (! match) {
                return this.text.trim() === '' ? this.clear() : this.revert();
            }

            const [jy, jm, jd] = match.slice(1).map(Number);

            if (jm < 1 || jm > 12 || jd < 1 || jd > monthLength(jy, jm)) {
                return this.revert();
            }

            this.viewYear = jy;
            this.viewMonth = jm;
            this.commit([jy, jm, jd]);
        },

        revert() {
            const current = this.selected();

            this.text = current ? toFa(`${current[0]}/${pad(current[1])}/${pad(current[2])}`) : '';
        },

        shiftMonth(delta) {
            const index = this.viewYear * 12 + (this.viewMonth - 1) + delta;

            this.viewYear = Math.floor(index / 12);
            this.viewMonth = (index % 12) + 1;
        },

        shiftYear(delta) {
            this.viewYear += delta;
        },

        prev() {
            if (this.mode === 'years') {
                this.yearPage -= 12;
            } else if (this.mode === 'months') {
                this.shiftYear(-1);
            } else {
                this.shiftMonth(-1);
            }
        },

        next() {
            if (this.mode === 'years') {
                this.yearPage += 12;
            } else if (this.mode === 'months') {
                this.shiftYear(1);
            } else {
                this.shiftMonth(1);
            }
        },

        zoomOut() {
            if (this.mode === 'days') {
                this.mode = 'months';
            } else if (this.mode === 'months') {
                this.yearPage = this.viewYear - 4;
                this.mode = 'years';
            }
        },

        pickYear(year) {
            this.viewYear = year;
            this.mode = 'months';
        },

        pickMonth(month) {
            this.viewMonth = month;
            this.mode = 'days';
        },

        get title() {
            if (this.mode === 'years') {
                return `${toFa(this.yearPage)} – ${toFa(this.yearPage + 11)}`;
            }

            if (this.mode === 'months') {
                return toFa(this.viewYear);
            }

            return `${MONTHS[this.viewMonth - 1]} ${toFa(this.viewYear)}`;
        },

        get years() {
            const current = this.selected();
            const today = toJalali(...this.todayGregorian());

            return Array.from({ length: 12 }, (_, index) => {
                const year = this.yearPage + index;

                return {
                    year,
                    label: toFa(year),
                    selected: !! current && current[0] === year,
                    today: today[0] === year,
                    muted: year < minYear || year > this.upperYear,
                };
            });
        },

        get monthCells() {
            const current = this.selected();
            const today = toJalali(...this.todayGregorian());

            return MONTHS.map((name, index) => ({
                month: index + 1,
                name,
                selected: !! current && current[0] === this.viewYear && current[1] === index + 1,
                today: today[0] === this.viewYear && today[1] === index + 1,
            }));
        },

        get cells() {
            const blanks = weekdayColumn(this.viewYear, this.viewMonth, 1);
            const total = monthLength(this.viewYear, this.viewMonth);
            const selected = this.selected();
            const today = toJalali(...this.todayGregorian());
            const cells = Array.from({ length: blanks }, () => null);
            const bounds = selected && this.peer ? (this.role === 'end' ? [this.peer, selected] : [selected, this.peer]) : null;

            for (let day = 1; day <= total; day++) {
                const column = (blanks + day - 1) % 7;
                const date = [this.viewYear, this.viewMonth, day];

                cells.push({
                    day,
                    label: toFa(day),
                    aria: `${WEEKDAY_NAMES[column]} ${toFa(day)} ${MONTHS[this.viewMonth - 1]} ${toFa(this.viewYear)}`,
                    selected: !! selected && compare(selected, date) === 0,
                    today: compare(today, date) === 0,
                    friday: column === 6,
                    disabled: this.isDisabled(day),
                    inRange: !! bounds && compare(date, bounds[0]) >= 0 && compare(date, bounds[1]) <= 0,
                    peer: !! this.peer && compare(this.peer, date) === 0,
                });
            }

            return cells;
        },
    };
}
