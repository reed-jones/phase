import app from '../../../resources/js/app';
import { followAllRedirects } from "@phased/phase/routes";

// Enable Axios Redirects...
followAllRedirects(app.$router)

app.$mount('#app');
