import { StyleSheet } from 'react-native'

export default StyleSheet.create({
    container:
    {
        flex: 1,
    },
    _bgColor:
    {
        backgroundColor: '#ee1d23',
        position: 'relative',
        flex: 1
    },

    _upperView:
    {
        height: 150,
        position: 'relative',
        justifyContent: 'center',
        alignItems: 'center',

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

    _lowerView:
    {
        backgroundColor: '#fff',
        flex: 1,
        borderTopRightRadius: 100,
        position: 'relative',
        justifyContent: 'center',
        alignItems: 'center',
        overflow: 'hidden',
        padding: 10
    },

    _view: {
        width: '100%',

        _txt_input_view: {
            marginTop: '5%',
            width: '100%',

            _view: {
                width: '100%',

                _txt: {
                    fontWeight: '500',
                    fontSize: 14,
                    height: 40,
                    paddingLeft: 0,
                    color: '#21211F'

                },
                _floating_txt: {
                    fontWeight: '400',
                    fontSize: 8,
                    color: '#C1C1C1',
                    marginBottom: -10
                },
                _icon: {
                    height: 18,
                    width: 18,
                    position: 'absolute',
                    bottom: 5,
                    right: 0
                },
                _under_line: {
                    borderBottomColor: 'gray',
                    borderTopColor: 'transparent',
                    borderLeftColor: 'transparent',
                    borderRightColor: 'transparent',
                    borderWidth: 1,
                    marginTop: -10
                }
            }
        },
        _input: {
            height: 55,
            width: '100%',
            borderRadius: 25,
            backgroundColor: '#fff',
            borderColor: '#a8a8a8',
            borderWidth: 1,
            justifyContent: 'center',
            paddingLeft: 10,
            paddingRight: 10,
            position: 'relative',
            marginTop: 20
        },

        _btn: {
            backgroundColor: '#ee1d23',
            height: 45,
            width: '100%',
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
    _get_in_touch: {
        color: '#EE1D23',
        fontSize: 18,
        fontWeight: '600',
        marginTop: '10%'
    },
    _scroll: {
        width: '90%'
    },
    _margin_top: {
        marginTop: '5%'
    }

})