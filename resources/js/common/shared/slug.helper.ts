class SlugHelper {
    private sanitizeStr(str: string): string {
        const letterARegex = /a|à|á|ả|ã|ạ|ă|ằ|ắ|ẳ|ẵ|ặ|â|ầ|ấ|ẩ|ẫ|ậ/gi;
        const letterDRegex = /đ/gi;
        const letterERegex = /e|è|é|ẻ|ẽ|ẹ|ê|ề|ế|ể|ễ|ệ/gi;
        const letterIRegex = /i|ì|í|ỉ|ĩ|ị/gi;
        const letterORegex = /o|ò|ó|ỏ|õ|ọ|ô|ồ|ố|ổ|ỗ|ộ|ơ|ờ|ớ|ở|ỡ|ợ/gi;
        const letterURegex = /u|ù|ú|ủ|ũ|ụ|ư|ừ|ứ|ử|ữ|ự/gi;
        const letterYRegex = /y|ỳ|ý|ỷ|ỹ|ỵ/gi;

        return str
            .toLocaleLowerCase()
            .replace(letterARegex, 'a')
            .replace(letterDRegex, 'd')
            .replace(letterERegex, 'e')
            .replace(letterIRegex, 'i')
            .replace(letterORegex, 'o')
            .replace(letterURegex, 'u')
            .replace(letterYRegex, 'y')
            .replace(/\s*$/g, '')
            .replace(/[^\w\s]/g, '')
            .replace(/\s+/g, '-')
    }

    public slugify = (str: string): string => {
        return this.sanitizeStr(str);
    }
}

export default new SlugHelper();
