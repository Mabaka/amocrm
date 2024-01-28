import {AmoCRM} from './amocrm.service';

const server = import.meta.env.VITE_SERVER;
const port = import.meta.env.VITE_PORT;

export default {
    amoCrm: new AmoCRM(server + ':' + port + '/api'),
}