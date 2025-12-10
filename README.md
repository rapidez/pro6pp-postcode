# Rapidez pro6pp-postcode

Support postcode lookup using pro6pp.nl

## Installation

```
composer require rapidez/pro6pp-postcode
```

## Configuration

Add your credentials in the `.env`
```env
PRO6PP_API_KEY=
```

The key that needs to be used might also be called `auth_key`

## Customisation

In case you have your own postcode fields you want checked and updated you can emit the `postcode-change` event passing a reactive object with the following keys:

- `country_id/country_code`
- `postcode`
- `street[0]`
- `street[1]`
- `city`

Then you can use it like:

```html
<input 
    v-on:change="window.$emit('postcode-change', addressVariables)" 
    name="postcode" 
    label="Postcode" 
    v-model="addressVariables.postcode" 
    required
/>
<input 
    v-on:change="window.$emit('postcode-change', addressVariables)" 
    name="street[1]" 
    type="number" 
    label="House number" 
    v-model="addressVariables.street[1]" 
    placeholder=""
/>
```

## Note

Currently only Dutch address completion is implemented!

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
