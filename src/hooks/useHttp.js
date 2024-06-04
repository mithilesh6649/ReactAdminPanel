
import axios from "axios";
import { useState,useEffect } from "react";
const useHttp = (request) =>{
 
  const [httpResponse,setHttpResponse] = useState(null);
  const [httpError,setHttpError] = useState(null);
  const [httpLoader,setHttpLoader] = useState(true);
  
    const ajax = () =>{
         axios(request)
         .then((response)=>{
           setHttpResponse(response.data);
         })
         .catch((error)=>{
            setHttpError(error.response);
         })
        .finally(()=>{
            setHttpLoader(false);
         });
    }

    useEffect(()=>{
        ajax();
    },[request]);
    return [httpResponse , httpError , httpLoader];
}

export default useHttp;
