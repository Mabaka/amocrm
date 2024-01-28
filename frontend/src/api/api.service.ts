import axios, { AxiosResponse } from "axios";

class ApiError extends Error {
    response: AxiosResponse<any, any>;
    
    constructor(message:string,response:AxiosResponse){
        super(message);
        this.response=response;
    }
};

export class ApiService{
    _getError(e:any){
        if(e.isAxiosError){
            return new ApiError(e.response.data?.error?.message ?? e.message,e.response);            
        }else{
            return new ApiError(e.message,e.response);
        }
    };

    _request(method:any, url:string) {
        return async () => {
          try {
            const response = await method(url);
            return {
              __state: "success",
              ...response,
            };
          } catch (e:any) {
            return {
              __state: "error",
              data: this._getError(e),
            };
          }
        };
      }

    _request_b(method:any,url:string,body:object,headers:object){
        return async()=>{
            try{
                const response = await method(url,body,headers);
                return{
                    __state: "success",
                    ...response
                }
            }catch (e:any){
                return{
                    __state: "error",
                    data: this._getError(e)
                };
            };
        };
    };

    $get(url:string){
        return this._request(axios.get,url)();
    }

    $post(url:string,body:object,headers:object={}){
        return this._request_b(axios.post,url,body,headers)();
    }
}