import Menu from "../../Json-api/Menu.json";
import {
  Button,
  Container,
  Stack
} from '@mui/material';

import { Link } from "react-router-dom";
const Navbar = () => {

  const Buttons = ({ data }) => {
    const buttonDesign = (
      <>
        <Link to={data.url} sx={{
          borderRadius: "0",
          "&:hover": {
            transition: "0.5s",
            backgroundColor: "secondary.main",
            color: "white"
          }
        }}>{data.label}</Link>
      </>
    );
    return buttonDesign;
  }

  const desing = (
    <>
      <Stack className='bg-light'>
        <Container className='bg-light'>
          <Stack direction={{
            xs: "column",
            sm: "row"
          }} justifyContent="space-between">
            <h4>Testing</h4>
            <Stack direction={{
              xs: "column",
              sm: "row"
            }} spacing={1}>
              {
                Menu.map((menu) => {
                  return <Buttons key={menu.id} data={menu} />
                })
              }
            </Stack>
          </Stack>
        </Container>
      </Stack>
    </>
  );

  return desing;

}

export default Navbar;