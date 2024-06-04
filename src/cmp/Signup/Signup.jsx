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
import { useState, useEffect } from "react";
import {
    Link
} from "react-router-dom";
import useHttp from '../../hooks/useHttp';
import MediaQuery from "react-responsive";
import { Password } from "@mui/icons-material";
import SweetAlert from 'react-bootstrap-sweetalert';
const Signup = () => {


    const signupForm = {
        fullname: "Mithilesh Kumar",
        mobile: "7651823456",
        email: "mk@gmail.com",
        password: "66546546546"
    }

    const signupFormError = {
        fullname: {
            state: false,
            message: ""
        },
        mobile: {
            state: false,
            message: ""
        },
        email: {
            state: false,
            message: ""
        },
        password: {
            state: false,
            message: ""
        }
    }

    const [input, setInput] = useState(signupForm);
    const [error, setError] = useState(signupFormError);
    const [checked, setChecked] = useState(false);
    const [request, setRequest] = useState(null);
    const [sweetAlert, setSweetAlert] = useState({
        state: false,
        title: "s",
        icon: "controlled",
        message: " s"
    });
    const [httpResponse, httpError, httpLoader] = useHttp(request);
    useEffect(() => {

        if (request) {
            if (httpResponse) {
                return setSweetAlert({
                    state: true,
                    title: "Success",
                    icon: "success",
                    message: "Signup is completed try to login"
                });
            }
            if (httpError) {
                return setSweetAlert({
                    state: true,
                    title: "Failed",
                    icon: "error",
                    message: "Something went to wrong"
                });
            }
        }
    }, [httpResponse, httpError, httpLoader]);
    const Alert = () => {
        const alert = (
            <>
                <SweetAlert
                    show={sweetAlert.state}
                    title={sweetAlert.title}
                    type={sweetAlert.icon}
                    customButtons={
                        <>
                            <Button variant="outlined" color="warning" sx={{ py: 1, mr: 2 }}>Cancel</Button>
                            <Button variant="outlined" color="success" sx={{ py: 1 }}>OK</Button>
                        </>

                    }
                >
                    {sweetAlert.message}
                </SweetAlert>
            </>
        );
        return alert;
    }

    const fullnameValidation = (e) => {
        const input = e.target;
        const key = input.name;
        const isRequired = required(input);
        return setError((oldData) => {
            return {
                ...oldData,
                [key]: isRequired
            }
        });
        console.log(isRequired);
    }


    const required = (input) => {
        const value = input.value.trim();
        if (value.length === 0) {
            return {
                state: true,
                message: "This field is required"
            }
        } else {
            return {
                state: false,
                message: ""
            }
        }
    }

    const emailSyntax = (input) => {
        const value = input.value.trim();
        const regExp = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/g;
        if (regExp.test(value)) {
            return {
                state: false,
                message: ""
            }
        } else {
            return {
                state: true,
                message: "Not a valid email"
            }
        }
    }

    const minLength = (input, minLengthRange) => {
        const value = input.value.trim();
        if (value.length < minLengthRange) {
            return {
                state: true,
                message: `Minumum ${minLengthRange} characters is required`
            }
        } else {
            return {
                state: false,
                message: ""
            }
        }
    }


    const maxLength = (input, maxLengthRange) => {
        const value = input.value.trim();
        if (value.length > maxLengthRange) {
            return {
                state: true,
                message: `Maximum ${maxLengthRange} characters is required`
            }
        } else {
            return {
                state: false,
                message: ""
            }
        }
    }

    const mobileValidation = (e) => {
        const input = e.target;
        const key = input.name;
        const isRequired = required(input);
        const isMinLength = minLength(input, 4);
        const isMaxLength = maxLength(input, 10);
        console.log(isMinLength);
        return setError((oldData) => {
            return {
                ...oldData,
                [key]: (isRequired.state && isRequired) || (isMinLength.state && isMinLength) || isMaxLength
            }
        });
    }

    const emailValidation = (e) => {
        const input = e.target;
        const key = input.name;
        const isEmail = emailSyntax(input);
        const isRequired = required(input);

        return setError((oldData) => {
            return {
                ...oldData,
                [key]: (isRequired.state && isRequired) || isEmail
            }
        });
    }

    const passwordValidation = (e) => {
        const input = e.target;
        const key = input.name;
        const isRequired = required(input);
        const isMinLength = minLength(input, 8);
        const isMaxLength = maxLength(input, 15);
        console.log(isMinLength);
        return setError((oldData) => {
            return {
                ...oldData,
                [key]: (isRequired.state && isRequired) || (isMinLength.state && isMinLength) || isMaxLength
            }
        });
    }

    // console.log("mksssss", error.fullname.state);

    const updateValue = (e) => {
        const input = e.target;
        const key = input.name;
        const value = input.value;
        return setInput((oldData) => {
            return {
                ...oldData,
                [key]: value
            }
        });
    }

    const validateOnSubmit = () => {
        let valid = true;
        for (const key in input) {
            if (input[key].length === 0) {
                valid = false;
                setError((oldData) => {
                    return {
                        ...oldData,
                        [key]: {
                            state: true,
                            message: "This field is required"
                        }
                    }
                });
            }
        }

        return valid;
    }

    const register = (e) => {
        e.preventDefault();
        const isValid = validateOnSubmit();
        if (isValid) {
            setRequest({
                method: "post",
                url: "http://localhost:3434/signup",
                data: input
            })
        }
    }

    const design = (
        <>

            <Grid container>
                <Alert></Alert>
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
                    <form onSubmit={register}>
                        <Stack direction="column" spacing={3}>
                            <TextField error={error.fullname.state} helperText={error.fullname.message} label="Fullname" variant="outlined" name="fullname" value={input.fullname} onChange={updateValue} onBlur={fullnameValidation} onInput={fullnameValidation} />
                            <TextField error={error.mobile.state} helperText={error.mobile.message} type="number" label="Mobile" variant="outlined" name="mobile" value={input.mobile} onChange={updateValue} onBlur={mobileValidation} onInput={mobileValidation} />
                            <TextField error={error.email.state} helperText={error.email.message} label="Email" variant="outlined" name="email" value={input.email} onChange={updateValue} onBlur={emailValidation} onInput={emailValidation} />
                            <TextField error={error.password.state} helperText={error.password.message} type="password" label="Password" variant="outlined" name="password" value={input.password} onChange={updateValue} onBlur={passwordValidation} onInput={passwordValidation} />
                            <Stack direction="row" justifyContent="space-between" alignItems="center">
                                <FormGroup>
                                    <FormControlLabel
                                        label="I accept terms and conditions"
                                        control={<Checkbox checked={checked} onClick={() => { setChecked(!checked) }} color="warning" />}
                                    />
                                </FormGroup>
                                <Button type="button" component={Link} to="login">Already have an account</Button>
                            </Stack>
                            <div>
                                <Button type="submit" sx={{ py: 1 }} variant="contained" disabled={error.fullname.state || error.mobile.state || error.email.state || error.password.state || !checked}>Signup</Button>
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
