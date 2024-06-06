import Cookies from 'js-cookie'

/**
 * Store geolocation in localStorage
 * @todo Записывать данные в БД
 */
if (!Cookies.get('beeclub-latitude') && !Cookies.get('beeclub-longitude')) {
    navigator.geolocation.getCurrentPosition(position => {
        Cookies.set('beeclub-latitude', position.coords.latitude, {expires: 1})
        Cookies.set('beeclub-longitude', position.coords.longitude, {expires: 1})
        // @todo Вывод из env
        const apiKey = '0b1e09d7-c605-4f80-b836-6021778acfdb'
        const url = `https://geocode-maps.yandex.ru/1.x/?format=json&apikey=${apiKey}&geocode=${position.coords.longitude},${position.coords.latitude}`
        getData(url)
            .then(data => {
                const obj = data.response.GeoObjectCollection.featureMember[0].GeoObject
                Cookies.set('beeclub-city', obj.description, {expires: 1})
                Cookies.set('beeclub-street', obj.name, {expires: 1})
            });
    }, e => {
        if (e.code === 1) {
            console.warn('Отказано в разрешении геопозии')
        } else if (e.code === 2) {
            console.warn('Геопозиция недоступна')
        } else {
            console.warn(e)
        }
    })
}

async function getData(url = '') {
    const response = await fetch(url, {
        method: 'GET',
        mode: 'cors',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json'
        },
    })
    return response.json()
}


