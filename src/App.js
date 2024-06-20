import {
 BrowserRouter as  Router,
 Routes,
 Route
} from  "react-router-dom";

import {
  ThemeProvider,
  createTheme
} from "@mui/material";

import { 
  deepPurple,
  teal,
  pink,
  deepOrange,
  lightBlue,
  cyan
 } from '@mui/material/colors';

import Signup from './cmp/Signup/Signup';
import Admin from "./cmp/Admin/Admin";
import Dashboard from "./cmp/Admin/Dashboard/Dashboard";
import Login from "./cmp/Login/Login";
import Notfound from "./cmp/Notfound/Notfound";
import AuthGuard from "./guard/AuthGuard";
import '@fontsource/poppins';

function App() {
   
  const Theme = createTheme({
    palette:{
      primary:deepPurple,
      secondary:teal,
      error:pink,
      warning:deepOrange,
      success:cyan,
      info:lightBlue
    },
    typography:{
      fontFamily:"Poppins"
    }
  });


  return (
    <>
    <ThemeProvider theme={Theme}>
       <Router>
         <Routes>
          <Route path="/" element={ <Signup /> } /> 
          <Route element={<AuthGuard />}>
            <Route path="admin-panel" element={ <Admin /> } >
              <Route path="dashboard" element={<Dashboard />}/>
              <Route path="*" element={ <Notfound /> } />
            </Route>
          </Route>
          <Route path="login" element={ <Login /> } />
          <Route path="*" element={ <Notfound /> } /> 
         </Routes> 
       </Router> 
    </ThemeProvider>
    </>
  );
}

export default App;
