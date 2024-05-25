import sys
from yt_dlp import YoutubeDL

def download_music(song_name):
    ydl_opts = {
        'format': 'bestaudio/best',
        'postprocessors': [{
            'key': 'FFmpegExtractAudio',
            'preferredcodec': 'mp3',
            'preferredquality': '192',
        }],
        'noplaylist': True,
        # 设置文件输出模板
        'outtmpl': 'downloads/%(title)s.%(ext)s',
    }

    with YoutubeDL(ydl_opts) as ydl:
        info_dict = ydl.extract_info(f"ytsearch1:{song_name}", download=True)
        video_title = info_dict['entries'][0]['title']  # 获取视频标题
        # 假设下载后的文件名
        return f"downloads/{video_title}.mp3"

if __name__ == '__main__':
    song_name = ' '.join(sys.argv[1:])
    file_name = download_music(song_name)
    # 确保这里只输出文件名
    print(file_name)