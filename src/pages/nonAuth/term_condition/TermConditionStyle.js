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

        _scroll: {
            width: '90%',
            //marginTop: '5%',
            marginBottom: '0%',

            _title: {
                marginTop: '5%',
                textAlign: 'center',
                fontSize: 16,
                fontWeight: '600',
                color: '#EE1D23',
                marginBottom: '5%'
            },

            _content: {
                fontSize: 12,
                textAlign: 'justify',
                color: '#000000'
            }
        }
    }
})