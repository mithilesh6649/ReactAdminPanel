import Navbar from "../Navbar/Navbar";
import { Outlet, Link } from "react-router-dom";
import { useState } from "react";
import {
  Drawer,
  Box,
  List,
  ListItem,
  ListItemButton,
  ListItemIcon,
  ListItemText,
  Stack,
  AppBar,
  Toolbar,
  IconButton,
  Button
} from "@mui/material";
import DashboardIcon from '@mui/icons-material/Dashboard';
import MenuIcon from '@mui/icons-material/Menu';
import LogoutIcon from '@mui/icons-material/Logout';
const Admin = () => {

  const [active, setActive] = useState(false);

  const htmlDesign = (
    <>
      {/* <Navbar />
            <Outlet /> */}
      <Drawer open={active} anchor="left" onClose={() => setActive(!active)}>
        <Box sx={{ width: 250 }}>
          <List>
            <ListItem disablePadding>
              <ListItemButton component={Link} to="dashboard" >
                <ListItemIcon>
                  <DashboardIcon />
                </ListItemIcon>
                <ListItemText primary="Dashboard" />
              </ListItemButton>
            </ListItem>
          </List>
        </Box>
      </Drawer>
      <Stack>
        <AppBar position="static">
          <Stack direction="row" justifyContent="space-between" alignContent="center">
            <Toolbar>
              <IconButton color="inherit">
                <MenuIcon onClick={() => setActive(!active)} />
              </IconButton>


            </Toolbar>

            <Toolbar>

              <IconButton color="inherit">
                <LogoutIcon />
                <Button color="inherit">Logout</Button>
              </IconButton>
            </Toolbar>
          </Stack>
        </AppBar>

        <Box sx={{ p: 4 }}>
          <Outlet />
        </Box>
      </Stack>
    </>
  );

  return htmlDesign;
}

export default Admin;