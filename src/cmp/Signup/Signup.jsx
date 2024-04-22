import {
    Button,
    Typography,
    Grid,
    TextField,
    Checkbox,
    FormGroup,
    FormControlLabel,
    Stack
} from "@mui/material";

import {
    Link
} from "react-router-dom";

import MediaQuery from "react-responsive";

const Signup = () => {
    const design = (
        <>
            <Grid container>
                <Grid item>
                    <MediaQuery minWidth={1224}>
                        <img src="images/auth.svg" alt="auth" width="100%" />
                    </MediaQuery>
                    <MediaQuery maxWidth={1224}>
                        <img src="images/mobile-auth.png" alt="auth" width="100%" />
                    </MediaQuery>
                </Grid>
                <Grid item sx={{ p: 5 }}>
                    <Typography variant="h4" sx={{ mb: 5 }}>
                        Register
                    </Typography>
                    <form>
                        <Stack direction="column" spacing={3}>
                            <TextField label="Fullname" variant="outlined" />
                            <TextField type="number" label="Mobile" variant="outlined" />
                            <TextField label="Email" variant="outlined" />
                            <TextField type="password" label="Password" variant="outlined" />
                            <Stack direction="row" justifyContent="space-between" alignItems="center">
                                <FormGroup>
                                    <FormControlLabel
                                        label="I accept terms and conditions"
                                        control={<Checkbox color="warning" />}
                                    />
                                </FormGroup>
                                <Button type="button" component={Link} to="login">Already have an account</Button>
                            </Stack>
                            <div>
                                <Button type="submit" sx={{ py: 1 }} variant="contained">Signup</Button>
                            </div>
                        </Stack>
                    </form>
                </Grid>
            </Grid>
        </>
    );
    return design;
}
export default Signup;
