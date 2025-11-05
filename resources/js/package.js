import { useDebounceFn, useMemoize } from '@vueuse/core'
import { on } from 'Vendor/rapidez/core/resources/js/polyfills/emit.js'

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
            address.city = ''
            address.street[0] = ''
        }
        return
    }

    address.city = foundAddress.city
    address.street[0] = foundAddress.street
}

on('postcode-change', useDebounceFn(updateAddressFromPro6pp, 100), { autoremove: false })
