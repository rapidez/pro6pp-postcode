import { set, useDebounceFn, useMemoize } from '@vueuse/core'

document.addEventListener('vue:loaded', function () {
    window.app.$on(
        'postcode-change',
        useDebounceFn(updateAddressFromPro6pp, 100),
    )
})

const getAddressFromPro6pp = useMemoize(async function (postcode, housenumber) {
    return window.rapidezAPI(
        'post',
        'pro6pp',
        {
            postcode: postcode,
            housenumber: housenumber,
        },
        { headers: { Accept: 'application/json' } },
    )
})

async function updateAddressFromPro6pp(address) {
    if ((address?.country_id || address?.country_code) != 'NL') {
        return
    }

    if (!address.postcode || !(address?.housenumber || address.street[1])) {
        return
    }

    let response = await getAddressFromPro6pp(
        address.postcode,
        address?.housenumber || address.street[1],
    )

    let foundAddress = response.results?.[0]

    if (!foundAddress?.city || !foundAddress?.street) {
        if (
            response?.error == 'nl_sixpp not found' ||
            response?.error == 'Streetnumber not found'
        ) {
            set(address, 'city', '')
            set(address.street, 0, '')
        }
        return
    }

    set(address, 'city', foundAddress.city)
    set(address.street, 0, foundAddress.street)
}
