import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
    },
    _bgColor: {
        backgroundColor: '#ee1d23',
        flex: 1
    },
    _upperView: {
        height: 150,
        justifyContent: 'center',
        alignItems: 'center',
        position: 'relative',

        _txt: {
            color: '#fff',
            fontSize: 20,
            fontWeight: '500'
        },
        _back: {
            position: 'absolute',
            top: 10,
            right: 10,

            _img: {
                height: 30,
                width: 30,
            }
        }
    },
    _lowerView: {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',
        alignItems: 'center',
        paddingTop: '5%',
        overflow: 'hidden',

        _scrollView: {
            width: '90%',
            borderTopRightRadius: 50,
            marginBottom: 10,
            _view: {
                alignItems: 'center'
            },
            _input: {
                height: 55,
                width: '90%',
                borderRadius: 25,
                backgroundColor: '#fff',
                borderColor: '#a8a8a8',
                borderWidth: 1,
                justifyContent: 'center',
                paddingLeft: 10,
                paddingRight: 10,
                position: 'relative',
                marginTop: 15,

                _txt_input: {
                    width: '75%'
                }
            },
            _input_area: {
                height: 100,
                width: '90%',
                borderRadius: 25,
                backgroundColor: '#fff',
                borderColor: '#a8a8a8',
                borderWidth: 1,
                padding: 10,
                marginTop: 15,
                paddingTop: 5
            },
            _dob: {
                height: '100%',
                width: 50,
                position: 'absolute',
                right: 0,
                justifyContent: 'center',
                alignItems: 'center',

                _img: {
                    height: 20,
                    width: 20
                }
            },
            _btn: {
                width: 80,
                height: 40,
                backgroundColor: 'red',
                borderRadius: 20,
                justifyContent: 'center',
                alignItems: 'center',
                position: 'absolute',
                top: 6,
                right: 5,

                _txt: {
                    color: '#fff',
                    fontSize: 12,
                    fontWeight: '500'
                }
            },
            _dob_txt: {
                marginTop: 10,
                marginBottom: 5,
                fontSize: 12,
                fontWeight: '500'
            },
            _dob_input: {
                flexDirection: 'row',
                justifyContent: 'space-between',
                width: '90%',

                _view: {
                    borderColor: '#a8a8a8',
                    borderWidth: 1,
                    borderRadius: 20,
                    height: 35,
                    flex: 1,
                    margin: 5,
                    justifyContent: 'center',

                    _txt: {
                        fontSize: 12,
                        fontWeight: '500',
                        marginLeft: 5
                    }
                }
            }

        },

        _btn: {
            backgroundColor: '#ee1d23',
            height: 45,
            width: '90%',
            borderRadius: 25,
            marginTop: '5%',
            justifyContent: 'center',
            alignItems: 'center',

            _txt: {
                color: '#fff',
                fontWeight: '600'
            }
        }
    },

    _otp_input_view: {
        width: '80%',
        height: 50,
        marginTop: '2%',

        _otp_input: {
            borderColor: '#a8a8a8',
            borderWidth: 1,
            borderRadius: 5,
            backgroundColor: '#fff',
            textAlign: 'center',
            fontSize: 16,
            fontWeight: '500',
            width: 45,
            height: 45,
        }
    },

    _enter_code_txt_view: {
        width: '80%',
        marginTop: 15,

        _enter_code: {
            fontSize: 14,
            fontWeight: '600',
            marginBottom: 5
        },

        _txt: {
            fontSize: 11
        }
    },

    _multi_select: {
        width: '90%',
        borderRadius: 25,
        backgroundColor: '#fff',
        borderColor: '#a8a8a8',
        borderWidth: 1,
        justifyContent: 'center',
        paddingLeft: 10,
        paddingRight: 10,
        position: 'relative',
        marginTop: 15,
        overflow: 'hidden',

        _txt_input: {
            width: '75%'
        }
    },
    _btn_section: {
        justifyContent: 'center',
        alignItems: 'center',
        marginTop: '0%',
        marginBottom: '5%',
        flexDirection: 'row',

        _txt: {
            color: '#000',
            fontSize: 12,
            fontWeight: '400'
        },
        _resend_btn: {
            color: '#000',
            fontSize: 15,
            fontWeight: '600',
            marginTop: -2,
            marginLeft: 5,
            textDecorationLine: 'underline',
            textDecorationStyle: 'solid',
            textDecorationColor: '#000',
        }
    }
})
