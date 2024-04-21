import {
 BrowserRouter as  Router,
 Routes,
 Route
} from  "react-router-dom";

import Signup from './cmp/Signup/Signup';
import Admin from "./cmp/Admin/Admin";
import Dashboard from "./cmp/Admin/Dashboard/Dashboard";
import Login from "./cmp/Login/Login";
import Notfound from "./cmp/Notfound/Notfound";
function App() {
  return (
    <>
       <Router>
         <Routes>
          <Route path="/" element={ <Signup /> } /> 
          <Route path="admin-panel" element={ <Admin /> } >
            <Route path="dashboard" element={<Dashboard />}/>
            <Route path="*" element={ <Notfound /> } />
          </Route>
          <Route path="login" element={ <Login /> } />
          <Route path="*" element={ <Notfound /> } /> 
         </Routes> 
       </Router> 
    </>
  );
}

export default App;
