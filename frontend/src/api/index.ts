import {AmoCRM} from './amocrm.service';

const server = import.meta.env.VITE_SERVER;
const port = import.meta.env.VITE_PORT;
const protocol = import.meta.env.VITE_PROTOCOL;

export default {
    amoCrm: new AmoCRM(protocol + '://' + server + ':' + port + '/api'),
}