import { ApiService } from "./api.service";

export class AmoCRM extends ApiService {
    path: string;
    constructor(path:string) {
      super();
      this.path = path;
    }
    
    leadAdd(params:object,headers:object={}){                
        return this.$post(`${this.path}/lead/add/`,params,headers);
    }

  }
  