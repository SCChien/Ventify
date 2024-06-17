import sys
import io
import json
from yt_dlp import YoutubeDL
import os
import re
import requests
import mysql.connector

# 强制使用UTF-8编码
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def safe_filename(filename):
    # 使用正则表达式移除所有特殊字符
    return re.sub(r'[\\/*?:"<>|]', "", filename)

def search_music(song_name, username):
    ydl_opts = {
        'format': 'bestaudio/best',
        'noplaylist': True,  # 不下载播放列表中的所有视频
        'quiet': True,  # 减少输出信息
    }

    with YoutubeDL(ydl_opts) as ydl:
        info_dict = ydl.extract_info(f"ytsearch20:{song_name}", download=False)
        results = []
        for entry in info_dict['entries'][:20]:  # 取前20个结果
            video_title = entry['title']
            results.append({
                'title': video_title,
                'url': entry['webpage_url']
            })
        # 保存前3个结果到数据库
        for entry in info_dict['entries'][:3]:
            save_recommendation(username, entry['title'], entry['webpage_url'])
        return results

def save_recommendation(username, title, url):
    conn = mysql.connector.connect(
        host='localhost',
        user='root',
        password='',
        database='music'
    )
    cursor = conn.cursor()
    
    # 获取当前用户的推荐歌曲数量
    cursor.execute("SELECT COUNT(*) FROM recommended_songs WHERE username = %s", (username,))
    count = cursor.fetchone()[0]

    # 如果达到或超过10首，删除最旧的3首
    if count >= 10:
        cursor.execute("DELETE FROM recommended_songs WHERE username = %s ORDER BY search_time ASC LIMIT 3", (username,))

    # 插入新的推荐歌曲
    cursor.execute("INSERT INTO recommended_songs (username, title, url) VALUES (%s, %s, %s)", (username, title, url))
    conn.commit()
    cursor.close()
    conn.close()

def download_song(url, download_dir):
    ydl_opts = {
        'format': 'bestaudio/best',
        'outtmpl': os.path.join(download_dir, '%(title)s.%(ext)s'),
        'postprocessors': [{
            'key': 'FFmpegExtractAudio',
            'preferredcodec': 'mp3',
            'preferredquality': '192',
        }],
        'writethumbnail': True,
        'ffmpeg_location': 'C:/ffmpeg',  # 替换为你安装ffmpeg的实际路径
        'quiet': False,  # 显示详细信息以进行调试
    }

    try:
        with YoutubeDL(ydl_opts) as ydl:
            info_dict = ydl.extract_info(url, download=True)
            thumbnail_url = info_dict.get('thumbnail')
            if thumbnail_url:
                thumbnail_ext = thumbnail_url.split('.')[-1]
                thumbnail_path = os.path.join(download_dir, f"{safe_filename(info_dict['title'])}.{thumbnail_ext}")
                with open(thumbnail_path, 'wb') as f:
                    f.write(requests.get(thumbnail_url).content)
        return "success"
    except Exception as e:
        return str(e)

if __name__ == '__main__':
    command = sys.argv[1]
    if command == "search":
        song_name = ' '.join(sys.argv[2:-1])
        username = sys.argv[-1]
        results = search_music(song_name, username)
        print(json.dumps(results, ensure_ascii=False))
    elif command == "download":
        url = sys.argv[2]
        download_dir = sys.argv[3]
        result = download_song(url, download_dir)
        print(result)
