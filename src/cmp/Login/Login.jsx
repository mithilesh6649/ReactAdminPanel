import {
    Grid,
    Stack,
    Button,
    Container,
    TextField,
    FormGroup,
    FormControlLabel,
    Checkbox
} from "@mui/material";
import {
    Link,
    useNavigate
} from "react-router-dom";


const Login = () => {

    const navigate = useNavigate();

    const login = (e) => {
        e.preventDefault();
        navigate('/admin-panel');
    }

    const htmlDesign = (
        <>

            <Container >
                <Grid container>
                    <Grid item xs={12} sm={6}> One</Grid>
                    <Grid item xs={12} sm={6}>
                        <h1>Login</h1>
                        <form onSubmit={login}>
                            <Stack direction="column" spacing={3}>
                                <TextField label="Username" variant="outlined" type="text" />
                                <TextField label="Password" variant="outlined" type="password" />
                                <Stack direction="row" justifyContent="end">
                                    <a href="signup">Forgot Password </a>
                                </Stack>
                                <Stack direction="row" justifyContent="space-between" alignItems="center">
                                    <FormGroup>
                                        <FormControlLabel control={<Checkbox defaultChecked />} label="Remember me" />
                                    </FormGroup>
                                    <Button type="submit" variant="contained" color="secondary" sx={{ px: 3, py: 1 }} > Login </Button>
                                </Stack>
                                <Link to="signup" >Create and account</Link>
                            </Stack>
                        </form>
                    </Grid>
                </Grid>
            </Container>


        </>
    );

    return htmlDesign;
}

export default Login;