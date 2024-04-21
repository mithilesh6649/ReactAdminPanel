import Navbar from "../Navbar/Navbar";
import {
  Outlet,
  Link,
  useResolvedPath,
  useMatch,
  useLocation
} from "react-router-dom";
import { useState } from "react";
import 'material-icons/iconfont/material-icons.css';
import AdminMenu from "../../Json-api/AdminMenu.json";
import MediaQuery from "react-responsive";
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
  Button,
  ListSubheader,
  Collapse,
  Avatar,
  MenuItem,
  Menu,
  Divider,
  Breadcrumbs,
  Typography
} from "@mui/material";
import DashboardIcon from '@mui/icons-material/Dashboard';
import MenuIcon from '@mui/icons-material/Menu';
import InboxIcon from '@mui/icons-material/Inbox';
import LogoutIcon from '@mui/icons-material/Logout';
import { deepOrange } from '@mui/material/colors';

const Admin = () => {

  const [active, setActive] = useState(true);
  const [activeOnMobile, setActiveOnMobile] = useState(false);
  const [width, setWidth] = useState(250);
  const [collapsible, setCollapsible] = useState(false);
  const [parent, setParent] = useState(null);
  const open = Boolean(parent);
  const location = useLocation();
  const routing = location.pathname.split('/');

  // Profile - control profile start
  const openProfileMenu = (e) => {
    const el = e.currentTarget;
    return setParent(el);

  }

  const closeProfileMenu = (e) => {
    return setParent(null);
  }
  // Profile - control profile end

  const Nav = ({ data }) => {

    const resolved = useResolvedPath(data.link ? data.link : false);
    const activeLink = useMatch({ path: resolved.pathname, end: true });

    const navDesign = (
      <>

        <ListItem>
          <ListItemButton

            sx={{ py: 1 }} onClick={data.isDropdown ? () => { setCollapsible(!collapsible) } : null}
            component={Link}
            to={data.link ? data.link : false}
            style={{
              backgroundColor: activeLink && data.link ? deepOrange[500] : null,
              color: activeLink && data.link ? "white" : null
            }}

          >
            <ListItemIcon>
              <span className="material-icons-outlined" style={{ color: activeLink && data.link ? "white" : null }}>
                {data.icon}
              </span>
            </ListItemIcon>
            <ListItemText primary={data.label} />
            {
              data.isDropdown ? (
                <>
                  <span className="material-icons-outlined  ">
                    expand_more
                  </span>
                </>
              ) : null
            }
          </ListItemButton>
        </ListItem>
        {
          data.isDropdown ? <Dropdown menu={data.dropdownMenu} /> : null
        }
      </>
    );
    return navDesign;
  }

  const controlDrawerOnDesktop = () => {


    return (
      setActive(!active),
      active ? setWidth(0) : setWidth(250)
    )
  }


  const controlDrawerOnMobile = () => {


    return (
      setActiveOnMobile(!activeOnMobile),
      activeOnMobile ? setWidth(0) : setWidth(250)
    )
  }



  const DesktopDrawer = () => {
    const tmp = (
      <>
        <Drawer open={active} variant="persistent" sx={{
          width: width,
          "& .MuiDrawer-paper": {
            width: width,
            bgcolor: "white",
          }
        }}>
          {
            AdminMenu.map((admin) => {
              return <MenuList key={admin.id} admin={admin} />
            })
          }

        </Drawer>
      </>
    );
    return tmp;
  }


  const MobileDrawer = () => {
    const tmp2 = (
      <>
        <Drawer open={activeOnMobile} variant="temporary" onClose={controlDrawerOnMobile} onClick={controlDrawerOnMobile} sx={{
          width: width,
          "& .MuiDrawer-paper": {
            width: width,
            bgcolor: "white",
          }
        }}>
          {
            AdminMenu.map((admin) => {
              return <MenuList key={admin.id} admin={admin} />
            })
          }

        </Drawer>
      </>
    );
    return tmp2;
  }

  const BreadLink = ({ data }) => {

    const desing = (
      <>
        <Typography style={{
          textTransform: "capitalize",
          color: data.index === data.length ? deepOrange[400] : null
        }}>{data.item}</Typography>
      </>
    );
    return desing;
  }


  const MenuList = ({ admin }) => {

    const menuDesign = (
      <>

        <List subheader={<ListSubheader>
          {admin.cat}
        </ListSubheader>} >
          {
            admin.menus.map((menus) => {
              return <Nav key={menus.id} data={menus} />
            })
          }
        </List >

      </>
    );
    return menuDesign;
  }

  const Dropdown = ({ menu }) => {
    const dropdownDesign = (
      <>
        <Collapse in={collapsible}>
          {
            menu.map((menu) => {
              return <Nav key={menu.id} data={menu} />;
            })
          }
        </Collapse>
      </>

    );
    return dropdownDesign;
  }

  const htmlDesign = (
    <>
      {/* <Navbar />
            <Outlet /> */}
      {/* <Drawer variant="permanent" anchor="left" onClose={() => setActive(!active)}>
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
      </Stack> */}

      <Stack>
        <MediaQuery minWidth={1224}>
          <DesktopDrawer />
        </MediaQuery>

        <MediaQuery maxWidth={1224}>
          <MobileDrawer />
        </MediaQuery>
        <AppBar color="inherit" elevation={0} position="fixed"
          sx={{
            width: {
              xs: "100%",
              md: `calc(100% - ${width}px)`
            },
            transition: "0.1s",
            pr: 4
          }}>

          <Stack direction="row" justifyContent="space-between">
            <Toolbar>
              <MediaQuery minWidth={1224}>
                <IconButton color="inherit" onClick={controlDrawerOnDesktop}>
                  {/* <MenuIcon /> */}
                  <span className="material-icons-outlined  ">
                    menu
                  </span>
                </IconButton>
              </MediaQuery>

              <MediaQuery maxWidth={1224}>
                <IconButton color="inherit" onClick={controlDrawerOnMobile}>
                  {/* <MenuIcon /> */}
                  <span className="material-icons-outlined  ">
                    menu
                  </span>
                </IconButton>
              </MediaQuery>
            </Toolbar>

            <Toolbar >
              <Stack spacing={2} direction="row" alignItems="center">
                <IconButton color="inherit" >
                  <span className="material-icons-outlined">
                    shopping_basket
                  </span>
                </IconButton>

                <IconButton color="inherit" >
                  <span className="material-icons-outlined">
                    mail
                  </span>
                </IconButton>

                <IconButton color="inherit" >
                  <span className="material-icons-outlined">
                    notifications
                  </span>
                </IconButton>

                <IconButton color="inherit" onClick={openProfileMenu}>
                  <span className="material-icons-outlined">
                    <Avatar src="https://mui.com/static/images/avatar/3.jpg" />
                  </span>
                </IconButton>
                <Menu
                  anchorEl={parent}
                  open={open} onClose={closeProfileMenu}
                  onClick={closeProfileMenu}
                  PaperProps={{
                    elevation: 0,
                    sx: {
                      overflow: 'visible',
                      filter: 'drop-shadow(0px 2px 8px rgba(0,0,0,0.32))',
                      mt: 1.5,
                      '& .MuiAvatar-root': {
                        width: 32,
                        height: 32,
                        ml: -0.5,
                        mr: 1,
                      },
                      '&::before': {
                        content: '""',
                        display: 'block',
                        position: 'absolute',
                        top: 0,
                        right: 14,
                        width: 10,
                        height: 10,
                        bgcolor: 'background.paper',
                        transform: 'translateY(-50%) rotate(45deg)',
                        zIndex: 0,
                      },
                    },
                  }}
                  transformOrigin={{ horizontal: 'right', vertical: 'top' }}
                  anchorOrigin={{ horizontal: 'right', vertical: 'bottom' }}


                >
                  <MenuItem >
                    <Avatar /> Profile
                  </MenuItem>
                  <MenuItem >
                    <Avatar /> My account
                  </MenuItem>
                  <Divider />
                  <MenuItem >
                    <ListItemIcon>
                      <span className="material-icons-outlined">
                        person_add
                      </span>
                    </ListItemIcon>
                    Add another account
                  </MenuItem>
                  <MenuItem >
                    <ListItemIcon>
                      <span className="material-icons-outlined">
                        settings
                      </span>
                    </ListItemIcon>
                    Settings
                  </MenuItem>
                  <MenuItem >
                    <ListItemIcon>
                      <span className="material-icons-outlined">
                        logout
                      </span>
                    </ListItemIcon>
                    Logout
                  </MenuItem>
                </Menu>
              </Stack>
            </Toolbar>

          </Stack>

        </AppBar>

        <Stack sx={{
          ml: {
            sx: 0,
            md: `${width}px`
          },
          mt: 5,
          p: 3,
          transition: "0.1s",
          bgcolor: "#f5f5f5",
          height: "100vh"
        }}>

          <Breadcrumbs aria-label="breadcrumb" sx={{ my: 4 }}>

            {
              routing.map((item, index) => {
                if (index > 0) {
                  return <BreadLink key={index} data={{
                    item: item,
                    index: index,
                    length: routing.length - 1
                  }} />
                }
              })
            }

          </Breadcrumbs>
          <Outlet />

        </Stack>

      </Stack>


    </>
  );

  return htmlDesign;
}

export default Admin;