import { Outlet, Navigate } from "react-router-dom";
import Cookies from "universal-cookie";
const AuthGuard = () => {
    const cookie = new Cookies();
    let isLogged = false;
    const user = cookie.get(cookie);
    if (user) {
        isLogged = true;
    }
    return isLogged ? <Outlet /> : <Navigate to='/login' />
}

export default AuthGuard;